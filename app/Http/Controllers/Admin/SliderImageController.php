<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

/**
 * Contrôleur SliderImageController
 *
 * Ce contrôleur gère les opérations liées aux images des sliders dans l'interface d'administration.
 * Il permet de :
 * - Afficher le formulaire de gestion des images
 * - Ajouter de nouvelles images aux sliders
 * - Mettre à jour l'ordre des images
 * - Supprimer des images
 * - Générer des noms de fichiers uniques
 *
 * Fonctionnalités principales :
 * - Gestion des images des sliders
 * - Validation des données
 * - Stockage sécurisé des fichiers
 * - Mise à jour de l'ordre des images
 * - Suppression des images
 */

class SliderImageController extends Controller
{
    /**
     * Affiche le formulaire pour gérer les images d'un slider
     */
    public function editImages(Slider $slider)
    {
        $this->authorize('update', $slider);
        return view('admin.sliders.images', compact('slider'));
    }

    /**
     * Ajoute une nouvelle image à un slider
     */
    public function storeImage(Request $request, Slider $slider)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $file = $request->file('image');

            // Récupérer le nom original et l'extension
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();

            // Nettoyer le nom du fichier
            $safeName = Str::slug($originalName) . '.' . $extension;
            $counter = 1;

            // Vérifier si le fichier existe déjà
            while (Storage::disk('public')->exists('sliders/' . $safeName)) {
                $safeName = Str::slug($originalName) . '-' . $counter . '.' . $extension;
                $counter++;
            }

            // Stocker le fichier
            $stored = $file->storeAs('public/sliders', $safeName);

            // Vérifications post-stockage
            $fileExists = Storage::disk('public')->exists('sliders/' . $safeName);
            $physicalExists = file_exists(storage_path('app/public/sliders/' . $safeName));

            if (!$fileExists || !$physicalExists) {
                // Essayer une méthode alternative de stockage
                if (!$file->move(storage_path('app/public/sliders'), $safeName)) {
                    throw new \Exception("Le fichier n'a pas pu être stocké correctement. Chemin: " . storage_path('app/public/sliders/' . $safeName));
                }
                $physicalExists = file_exists(storage_path('app/public/sliders/' . $safeName));
            }

            // Enregistrer dans la base de données
            $image = $slider->images()->create([
                'path' => 'sliders/' . $safeName,
                'format' => $extension,
                'type' => 'original',
                'position' => $slider->images()->count(),
                'imageable_type' => 'App\Models\Slider',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Image ajoutée avec succès au slider.',
                'image' => $image,
                'path' => $stored,
                'exists' => $physicalExists,
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur complète: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateOrder(Request $request, Slider $slider)
    {
        $this->authorize('update', $slider);

        $request->validate([
            'imageOrders' => 'required|array',
            'imageOrders.*' => 'integer|exists:images,id',
        ]);

        try {
            // Mettre à jour les positions dans la base de données
            foreach ($request->imageOrders as $imageId => $position) {
                $slider->images()->where('id', $imageId)->update(['position' => $position]);
            }

            // Réorganiser les images pour s'assurer qu'il n'y a pas de trous
            $images = $slider->images()->orderBy('position')->get();
            foreach ($images as $index => $image) {
                $image->update(['position' => $index]);
            }

            return response()->json([
                'success' => true,
                'message' => 'L\'ordre des images a été mis à jour avec succès.'
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur lors de la mise à jour de l'ordre des images: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Met à jour l'ordre des images
     */
    public function updateOrder(Request $request, Slider $slider)
    {
        $this->authorize('update', $slider);

        $request->validate([
            'imageOrders' => 'required|array',
            'imageOrders.*' => 'integer|exists:images,id',
        ]);

        try {
            foreach ($request->imageOrders as $imageId => $position) {
                $slider->images()->where('id', $imageId)->update(['position' => $position]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprime une image d'un slider
     */
    public function destroyImage(Slider $slider, Image $image)
    {
        $this->authorize('update', $slider);

        try {
            $image->delete();
            return back()->with('success', 'Image supprimée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }

    protected function generateImageName($originalName)
    {
        // Extraire l'extension
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);

        // Générer un nom de type slideX.jpg
        $counter = 1;
        $baseName = 'slide' . $counter . '.' . $extension;

        // Vérifier si le fichier existe déjà
        while (Storage::disk('public')->exists('sliders/' . $baseName)) {
            $counter++;
            $baseName = 'slide' . $counter . '.' . $extension;
        }

        return $baseName;
    }
}
