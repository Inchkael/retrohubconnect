<?php
// app/Http/Controllers/UserProfileController.php

namespace App\Http\Controllers;

use App\Services\GeocodingService;
use App\Models\User;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

/**
 * Contrôleur UserProfileController
 *
 * Ce contrôleur gère toutes les opérations liées au profil utilisateur,
 * y compris l'affichage, la mise à jour des informations, la gestion des images,
 * le géocodage des adresses et la gestion des catégories de services pour les prestataires.
 *
 * Fonctionnalités principales :
 * - Affichage du profil utilisateur
 * - Mise à jour des informations de profil
 * - Gestion des avatars et logos
 * - Géocodage des adresses pour les prestataires
 * - Gestion des photos pour les prestataires
 * - Gestion des catégories de services pour les prestataires
 * - Changement de mot de passe
 */

class UserProfileController extends Controller
{



    protected $geocodingService;

    public function __construct(GeocodingService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }


    /**
     * Affiche le profil de l'utilisateur (adapté au rôle)
     */
    public function showProfile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    /**
     * Met à jour le profil selon le rôle de l'utilisateur
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $isProvider = $user->role === 'PROVIDER';

        // Règles de validation
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'delete_image' => 'nullable|boolean',
        ];

        if ($isProvider) {
            $rules = array_merge($rules, [
                'description' => 'nullable|string',
                'address' => 'nullable|string|max:255',
                'mobile_phone' => 'nullable|string|max:20',
                'vat_number' => 'nullable|string|max:20',
                'website' => 'nullable|url',
            ]);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Champs communs
        $updateData = $request->only(['first_name', 'last_name']);

        // L'email ne peut être modifié
        if ($request->email !== $user->email) {
            return response()->json([
                'success' => false,
                'message' => "L'email ne peut pas être modifié car il sert d'identifiant unique."
            ], 422);
        }

        // Champs spécifiques selon le rôle
        if ($isProvider) {
            $updateData = array_merge($updateData, $request->only([
                'description', 'address', 'mobile_phone', 'vat_number', 'website'
            ]));
        }

        // Gestion de l'image (avatar/logo)
        if ($request->has('delete_image') || $request->hasFile('avatar')) {
            $storagePath = $isProvider ? 'logos' : 'avatars';

            // Suppression de l'ancienne image
            if ($request->input('delete_image') == '1' && $user->image) {
                $oldImage = $user->image;
                Storage::delete('public/' . $oldImage->path);
                $oldImage->delete();
            }

            // Upload de la nouvelle image
            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $baseName = Str::slug($user->first_name . '-' . $user->last_name) . '_' . time();
                $originalExtension = $file->getClientOriginalExtension();
                $originalPath = "{$storagePath}/{$baseName}-original.{$originalExtension}";
                $file->storeAs('public/' . $storagePath, "{$baseName}-original.{$originalExtension}");

                // Crée l'image source pour le traitement
                $sourcePath = $file->getRealPath();
                $imageInfo = getimagesize($sourcePath);
                $mime = $imageInfo['mime'];

                switch ($mime) {
                    case 'image/jpeg': $source = imagecreatefromjpeg($sourcePath); break;
                    case 'image/png':
                        $source = imagecreatefrompng($sourcePath);
                        imagealphablending($source, false);
                        imagesavealpha($source, true);
                        break;
                    case 'image/gif': $source = imagecreatefromgif($sourcePath); break;
                    case 'image/webp': $source = imagecreatefromwebp($sourcePath); break;
                    default: throw new \Exception("Type d'image non supporté : " . $mime);
                }

                // Génère les versions optimisées
                $this->generateOptimizedVersions($source, $baseName, $storagePath);

                // Sauvegarde l'image dans la table images
                $image = new Image();
                $image->path = $originalPath;
                $image->format = $originalExtension;
                $image->type = $isProvider ? 'logo' : 'avatar';
                $user->image()->save($image);

                imagedestroy($source);
            }
        }

        $user->update($updateData);

        // Géocoder l'adresse si elle a été modifiée ou si les coordonnées sont manquantes
        if ($isProvider && ($request->has('address') && ($user->wasChanged('address') || !$user->latitude || !$user->longitude))) {
            try {
                $result = $this->geocodingService->geocodeAddress($user->address);
                if ($result) {
                    $user->update([
                        'latitude' => $result['latitude'],
                        'longitude' => $result['longitude']
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error("Erreur de géocodage pour l'utilisateur {$user->id}: " . $e->getMessage());
            }
        }


        return response()->json([
            'success' => true,
            'message' => 'Votre profil a été mis à jour avec succès.',
            'redirect' => route('user.profile')
        ]);
    }

    /**
     * Télécharge et optimise l'avatar (pour les utilisateurs) ou le logo (pour les prestataires)
     */
    public function uploadAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $isProvider = $user->isProvider();
        $storagePath = $isProvider ? 'logos' : 'avatars';

        if ($request->hasFile('avatar')) {
            // Suppression de l'ancienne image
            if ($user->image) {
                $oldImage = $user->image;
                Storage::delete('public/' . $oldImage->path);
                $oldImage->delete();
            }

            $file = $request->file('avatar');
            $baseName = Str::slug($user->first_name . '-' . $user->last_name) . '_' . time();
            $originalExtension = $file->getClientOriginalExtension();
            $originalPath = "{$storagePath}/{$baseName}-original.{$originalExtension}";
            $file->storeAs('public/' . $storagePath, "{$baseName}-original.{$originalExtension}");

            // Crée l'image source pour le traitement
            $sourcePath = $file->getRealPath();
            $imageInfo = getimagesize($sourcePath);
            $mime = $imageInfo['mime'];

            switch ($mime) {
                case 'image/jpeg': $source = imagecreatefromjpeg($sourcePath); break;
                case 'image/png':
                    $source = imagecreatefrompng($sourcePath);
                    imagealphablending($source, false);
                    imagesavealpha($source, true);
                    break;
                case 'image/gif': $source = imagecreatefromgif($sourcePath); break;
                case 'image/webp': $source = imagecreatefromwebp($sourcePath); break;
                default: throw new \Exception("Type d'image non supporté : " . $mime);
            }

            // Génère les versions optimisées
            $this->generateOptimizedVersions($source, $baseName, $storagePath);

            // Sauvegarde l'image dans la table images
            $image = new Image();
            $image->path = $originalPath;
            $image->format = $originalExtension;
            $image->type = $isProvider ? 'logo' : 'avatar';
            $user->image()->save($image);

            imagedestroy($source);

            return response()->json([
                'success' => true,
                'message' => ($isProvider ? 'Votre logo' : 'Votre avatar') . ' a été mis à jour avec succès.',
                'image_url' => Storage::url($originalPath),
            ]);
        }
    }

    protected function generateOptimizedVersions($source, $baseName, $storagePath)
    {
        $targetSizes = [380, 540, 700];
        foreach ($targetSizes as $size) {
            $width = imagesx($source);
            $height = imagesy($source);
            $newHeight = (int)($height * ($size / $width));
            $resized = imagecreatetruecolor($size, $newHeight);

            // Préserver la transparence pour les PNG
            imagecolortransparent($resized, imagecolorallocatealpha($resized, 0, 0, 0, 127));
            imagealphablending($resized, false);
            imagesavealpha($resized, true);

            imagecopyresampled($resized, $source, 0, 0, 0, 0, $size, $newHeight, $width, $height);

            // Générer dans tous les formats supportés
            foreach (['avif', 'webp', 'png', 'jpg'] as $format) {
                $this->saveOptimizedImage($resized, $baseName, $size, $format, $storagePath);
            }

            imagedestroy($resized);
        }
    }

    protected function saveOptimizedImage($image, $baseName, $size, $format, $storagePath)
    {
        try {
            $formatPath = "{$storagePath}/{$baseName}-{$size}w.{$format}";
            $publicPath = public_path('storage/' . $formatPath);

            switch ($format) {
                case 'avif':
                    if (function_exists('imageavif')) {
                        imageavif($image, $publicPath, 70);
                    }
                    break;
                case 'webp':
                    imagewebp($image, $publicPath, 70);
                    break;
                case 'png':
                    imagepng($image, $publicPath, 7);
                    break;
                case 'jpg':
                    imagejpeg($image, $publicPath, 70);
                    break;
            }
        } catch (\Exception $e) {
            \Log::error("Erreur lors de la génération de l'image {$format}: " . $e->getMessage());
        }
    }

    public function uploadPhotos(Request $request)
    {
        if (Auth::user()->role !== 'PROVIDER') {
            return response()->json([
                'success' => false,
                'message' => 'Cette fonctionnalité est réservée aux prestataires.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $photos = json_decode($user->photos ?? '[]', true) ?: [];

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $filename = Str::slug($user->first_name . '-' . $user->last_name) . '_' . time() . '_' . Str::random(5) . '.' . $photo->getClientOriginalExtension();
                $photo->storeAs('public/photos', $filename);
                $photos[] = $filename;
            }

            $user->update(['photos' => json_encode($photos)]);

            return response()->json([
                'success' => true,
                'message' => 'Vos photos ont été ajoutées avec succès.',
                'photos' => $photos
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Aucune photo n\'a été téléchargée.'
        ], 400);
    }

    /**
     * Supprime une photo (uniquement pour les prestataires)
     */
    public function deletePhoto($photoIndex)
    {
        if (Auth::user()->role !== 'PROVIDER') {
            return response()->json([
                'success' => false,
                'message' => 'Cette fonctionnalité est réservée aux prestataires.'
            ], 403);
        }

        $user = Auth::user();
        $photos = $user->photos ?? [];

        if (!isset($photos[$photoIndex])) {
            return response()->json([
                'success' => false,
                'message' => 'Photo introuvable.'
            ], 404);
        }

        Storage::delete('public/photos/' . $photos[$photoIndex]);
        unset($photos[$photoIndex]);
        $photos = array_values($photos);

        $user->update(['photos' => $photos]);

        return response()->json([
            'success' => true,
            'message' => 'La photo a été supprimée avec succès.',
        ]);
    }

    /**
     * Récupère les catégories de services disponibles et celles sélectionnées par le prestataire.
     */
    public function getServiceCategories()
    {
        $user = Auth::user();
        $allCategories = \App\Models\ServiceCategory::all();
        $userCategories = $user->serviceCategories()->wherePivot('user_id', $user->id)->get();

        \Log::info('Catégories chargées : ', [
            'all_categories' => $allCategories,
            'user_categories' => $userCategories,
        ]);

        return response()->json([
            'all_categories' => $allCategories,
            'user_categories' => $userCategories,
        ]);
    }

    /**
     * Met à jour les catégories de services du prestataire.
     */
    public function updateServiceCategories(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'PROVIDER') {
            return response()->json([
                'success' => false,
                'message' => 'Cette fonctionnalité est réservée aux prestataires.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'selected_categories' => 'required|array',
            'selected_categories.*' => 'exists:service_categories,id',
            'new_categories' => 'nullable|array',
            'new_categories.*' => 'string|max:100|unique:service_categories,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Synchronisation des catégories existantes
        $user->serviceCategories()->sync($request->selected_categories);

        // Ajout des nouvelles catégories
        if ($request->filled('new_categories')) {
            foreach ($request->new_categories as $newCategoryName) {
                $newCategory = \App\Models\ServiceCategory::firstOrCreate(
                    ['name' => $newCategoryName],
                    ['is_validated' => false, 'icon' => 'tag'] // Icône par défaut
                );
                $user->serviceCategories()->attach($newCategory->id);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Vos catégories de services ont été mises à jour avec succès.',
        ]);
    }

    public function getUserServiceCategoryIds()
    {
        return response()->json(
            Auth::user()->serviceCategories()->pluck('service_categories.id')->toArray()
        );
    }
    /**
     * Affiche le formulaire de changement de mot de passe.
     */
    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    /**
     * Met à jour le mot de passe de l'utilisateur.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => [
                'required',
                'min:7',
                'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/',
            ],
            'password_confirmation' => 'required|same:password',
        ], [
            'password.regex' => 'Le mot de passe doit contenir au moins une lettre et un chiffre.',
            'password_confirmation.same' => 'Les nouveaux mots de passe ne correspondent pas.',
        ]);

        $user = Auth::user();

        // Vérifie que l'ancien mot de passe est correct
        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'L\'ancien mot de passe est incorrect.']);
        }

        // Vérifie que le nouveau mot de passe est différent de l'ancien
        if (strcmp($request->old_password, $request->password) === 0) {
            return back()->withErrors(['password' => 'Le nouveau mot de passe doit être différent de l\'ancien.']);
        }

        // Met à jour le mot de passe
        $user->password = Hash::make($request->password);
        $user->save();

        // Redirige vers le profil avec un message de succès
        return redirect()->route('user.profile')->with('success', 'Mot de passe mis à jour avec succès !');
    }
}
