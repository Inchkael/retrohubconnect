<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Notifications\CategoryTransferNotification;
use Illuminate\Support\Facades\Log;

/**
 * Contrôleur ServiceCategoryController
 *
 * Ce contrôleur gère toutes les opérations liées aux catégories de services.
 * Il permet de créer, lire, mettre à jour et supprimer des catégories,
 * ainsi que de gérer les images associées et les relations avec les prestataires.
 *
 * Fonctionnalités principales :
 * - Gestion CRUD des catégories de services
 * - Traitement des images (upload, suppression, optimisation)
 * - Validation des catégories
 * - Transfert des prestataires entre catégories
 * - Désignation d'une catégorie du mois
 * - Gestion des relations avec les prestataires
 */

class ServiceCategoryController extends Controller
{
    /**
     * Affiche la liste des catégories de services validées.
     */
    public function index()
    {
        $categories = ServiceCategory::where('is_validated', true)->paginate(9);
        return view('service_categories.index', compact('categories'));
    }

    /**
     * Affiche la liste de toutes les catégories de services (pour les admins).
     */
    public function adminIndex()
    {
        $categories = ServiceCategory::orderBy('name')->get();
        return view('admin.service_categories.index', compact('categories'));
    }

    /**
     * Affiche les détails d'une catégorie de service.
     */
    public function show($id, Request $request)
    {
        $category = ServiceCategory::with('images')->findOrFail($id);
        $categories = ServiceCategory::all();

        $providersQuery = User::where('role', 'PROVIDER')
            ->whereHas('serviceCategories', function($query) use ($id) {
                $query->where('service_category_id', $id);
            });

        if ($request->has('region') && !empty($request->region)) {
            $providersQuery->where('address', 'like', '%' . $request->region . '%');
            $region = $request->region;
        } else {
            $region = null;
        }

        $providers = $providersQuery->paginate(9);

        return view('service_categories.show', [
            'category' => $category,
            'categories' => $categories,
            'providers' => $providers,
            'region' => $region
        ]);
    }

    /**
     * Affiche le formulaire de création.
     */
    public function create()
    {
        if (auth()->user()->role !== 'ADMIN') {
            abort(403, 'Accès interdit.');
        }
        return view('service_categories.create');
    }

    /**
     * Affiche le formulaire d'édition.
     */
    public function edit($id)
    {
        if (auth()->user()->role !== 'ADMIN') {
            abort(403, 'Accès interdit.');
        }

        $category = ServiceCategory::with('images')->findOrFail($id);
        return view('service_categories.edit', compact('category'));
    }

    /**
     * Enregistre une nouvelle catégorie.
     */
    public function store(Request $request)
    {
        try {
            $validated = $this->validateRequest($request);

            // Ajouter la validation automatique pour les admins
            $validated['is_validated'] = true;

            DB::beginTransaction();

            $category = ServiceCategory::create($validated);

            // Traiter les images uploadées
            $this->processImages($request, $category);

            DB::commit();

            return redirect()
                ->route('home')  // Redirection vers la page d'accueil
                ->with('success', 'Catégorie créée avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Met à jour une catégorie existante.
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $this->validateRequest($request);
            $category = ServiceCategory::findOrFail($id);

            DB::beginTransaction();

            // Traiter les images
            $this->processImages($request, $category);

            $category->update($validated);

            DB::commit();

            return redirect()
                ->route('service_categories.show', $category->id)
                ->with('success', 'Catégorie mise à jour avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Valide la requête pour les images.
     */
    protected function validateRequest(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'image1' => [
                'nullable',
                'file',
                'max:2048',
                function ($attribute, $value, $fail) {
                    if ($value) $this->validateImage($value, $fail);
                },
            ],
            'image2' => [
                'nullable',
                'file',
                'max:2048',
                function ($attribute, $value, $fail) {
                    if ($value) $this->validateImage($value, $fail);
                },
            ],
            'image3' => [
                'nullable',
                'file',
                'max:2048',
                function ($attribute, $value, $fail) {
                    if ($value) $this->validateImage($value, $fail);
                },
            ],
        ]);
    }

    /**
     * Valide qu'un fichier est une image valide.
     */
    protected function validateImage($value, $fail)
    {
        if (!$value->isValid()) {
            return $fail('Fichier invalide.');
        }

        $mime = $value->getMimeType();
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($mime, $allowedMimes)) {
            return $fail('Seules les images JPEG, PNG, GIF et WebP sont autorisées.');
        }

        if (!getimagesize($value->getRealPath())) {
            return $fail('Le fichier n\'est pas une image valide.');
        }

        $imageInfo = getimagesize($value->getRealPath());
        if ($imageInfo[0] > 2000 || $imageInfo[1] > 2000) {
            return $fail('L\'image ne doit pas dépasser 2000x2000 pixels.');
        }
    }

    /**
     * Traite les images uploadées.
     */
    protected function processImages(Request $request, $category)
    {
        for ($i = 1; $i <= 3; $i++) {
            $imageField = "image{$i}";

            // Suppression d'image demandée
            if ($request->input("delete_{$imageField}") == '1') {
                $this->deleteCategoryImages($category, $i - 1);
            }
            // Nouvelle image uploadée
            elseif ($request->hasFile($imageField)) {
                $this->processAndSaveImage($request->file($imageField), $category, $i - 1);
            }
        }
    }

    /**
     * Traite et sauvegarde une image avec ses versions optimisées
     */
    protected function processAndSaveImage($image, $model, $position)
    {
        $sourcePath = $image->getRealPath();
        $imageInfo = getimagesize($sourcePath);
        $mime = $imageInfo['mime'];
        $baseName = uniqid();
        $originalExtension = $image->getClientOriginalExtension();

        // Déterminer le format principal
        $formatMap = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp'
        ];
        $primaryFormat = $formatMap[$mime] ?? 'jpg';

        // Créer le dossier s'il n'existe pas
        $storagePath = public_path('storage/service_category_images');
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0775, true);
        }

        // Sauvegarder l'image originale
        $originalPath = "service_category_images/{$baseName}-original.{$originalExtension}";
        $image->storeAs('service_category_images', "{$baseName}-original.{$originalExtension}", 'public');

        // Créer l'image originale dans la base
        $originalImage = new Image([
            'path' => $originalPath,
            'format' => $primaryFormat,
            'type' => 'original',
            'position' => $position
        ]);
        $model->images()->save($originalImage);

        // Créer l'image source pour le traitement
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

        // Générer les versions optimisées
        $this->generateOptimizedVersions($source, $baseName, $model, $position, $mime);

        imagedestroy($source);
    }

    /**
     * Génère les versions optimisées
     */
    protected function generateOptimizedVersions($source, $baseName, $model, $position, $mime)
    {
        $targetSizes = [380, 540, 700];
        foreach ($targetSizes as $size) {
            $width = imagesx($source);
            $height = imagesy($source);
            $newHeight = (int)($height * ($size / $width));
            $resized = imagecreatetruecolor($size, $newHeight);

            // Préserver la transparence pour les PNG
            if ($mime === 'image/png') {
                imagecolortransparent($resized, imagecolorallocatealpha($resized, 0, 0, 0, 127));
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
            }

            imagecopyresampled($resized, $source, 0, 0, 0, 0, $size, $newHeight, $width, $height);

            // Générer dans tous les formats supportés
            foreach (['avif', 'webp', 'png', 'jpg'] as $format) {
                $this->saveOptimizedImage($resized, $baseName, $size, $format, $model, $position);
            }

            imagedestroy($resized);
        }
    }

    /**
     * Sauvegarde une image optimisée
     */
    protected function saveOptimizedImage($image, $baseName, $size, $format, $model, $position)
    {
        try {
            $formatPath = "service_category_images/{$baseName}-{$size}w.{$format}";
            $publicPath = public_path('storage/' . $formatPath);

            switch ($format) {
                case 'avif':
                    if (function_exists('imageavif')) {
                        imageavif($image, $publicPath, 70);
                        $this->createImageRecord($model, $formatPath, $format, "{$size}w", $position);
                    }
                    break;
                case 'webp':
                    imagewebp($image, $publicPath, 70);
                    $this->createImageRecord($model, $formatPath, $format, "{$size}w", $position);
                    break;
                case 'png':
                    imagepng($image, $publicPath, 7);
                    $this->createImageRecord($model, $formatPath, $format, "{$size}w", $position);
                    break;
                case 'jpg':
                    imagejpeg($image, $publicPath, 70);
                    $this->createImageRecord($model, $formatPath, $format, "{$size}w", $position);
                    break;
            }
        } catch (\Exception $e) {
            // Ignorer les erreurs de format non supporté
            \Log::error("Erreur lors de la génération de l'image {$format}: " . $e->getMessage());
        }
    }

    /**
     * Crée un enregistrement d'image
     */
    protected function createImageRecord($model, $path, $format, $type, $position)
    {
        $image = new Image([
            'path' => $path,
            'format' => $format,
            'type' => $type,
            'position' => $position
        ]);
        $model->images()->save($image);
    }

    /**
     * Supprime les images d'une catégorie pour une position donnée
     */
    protected function deleteCategoryImages($category, $position)
    {
        $images = $category->images()->where('position', $position)->get();
        foreach ($images as $image) {
            $image->delete();
        }
    }

    public function destroy($id)
    {
        try {
            $category = ServiceCategory::findOrFail($id);

            // Vérifie si des prestataires sont associés à cette catégorie
            if ($category->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette catégorie est utilisée par des prestataires et ne peut pas être supprimée.'
                ], 400);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'La catégorie a été supprimée avec succès.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifie si la table images existe et est correctement configurée
     */
    protected function checkImagesTable()
    {
        if (!Schema::hasTable('images')) {
            throw new \Exception("La table 'images' n'existe pas. Veuillez exécuter les migrations.");
        }

        if (!Schema::hasColumns('images', ['imageable_id', 'imageable_type'])) {
            throw new \Exception("La table 'images' n'a pas les colonnes polymorphiques requises.");
        }
    }
    /**
     * Valider une catégorie
     */
    public function validateCategory($id)
    {
        $category = ServiceCategory::findOrFail($id);
        $category->update(['is_validated' => true]);

        return redirect()->route('admin.service_categories.index')->with('success', 'Catégorie validée avec succès.');
    }



    /**
     * Transférer les prestataires d'une catégorie à une autre
     */
    public function transferProviders(Request $request, $id)
    {
        $validated = $request->validate([
            'new_category_id' => 'required|exists:service_categories,id',
        ]);

        $oldCategory = ServiceCategory::findOrFail($id);
        $newCategory = ServiceCategory::findOrFail($validated['new_category_id']);

        // Transférer les prestataires
        foreach ($oldCategory->users as $user) {
            $user->serviceCategories()->detach($oldCategory->id);
            $user->serviceCategories()->attach($newCategory->id);

            // Envoyer un email de notification
            $user->notify(new CategoryTransferNotification($oldCategory, $newCategory));
        }

        return redirect()->route('admin.service_categories.index')->with('success', 'Prestataires transférés avec succès et notifiés.');
    }

    /**
     * Désigner une catégorie du mois
     */
    public function setAsMonthly($id)
    {
        // Retirer la désignation de la catégorie du mois actuelle
        ServiceCategory::where('is_monthly', true)->update(['is_monthly' => false]);

        // Désigner la nouvelle catégorie du mois
        $category = ServiceCategory::findOrFail($id);
        $category->update(['is_monthly' => true]);

        return redirect()->route('admin.service_categories.index')->with('success', 'Catégorie désignée comme catégorie du mois.');
    }




}
