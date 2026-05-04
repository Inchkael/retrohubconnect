<?php
// app/Http/Controllers/Admin/SliderController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\Image as ImageModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

/**
 * Contrôleur SliderController
 *
 * Ce contrôleur gère les opérations CRUD pour les sliders dans l'interface d'administration.
 * Fonctionnalités principales :
 * - Création et mise à jour des sliders avec gestion des images
 * - Gestion des positions et de l'ordre d'affichage
 * - Validation complète des données
 * - Gestion robuste des erreurs avec logs détaillés
 */
class SliderController extends Controller
{
    /**
     * Afficher la liste des sliders
     */
    public function index()
    {
        $sliders = Slider::with(['images' => function($query) {
            $query->where('type', 'original')->orderBy('position');
        }])
            ->orderBy('position')
            ->paginate(10);

        return view('admin.sliders.index', compact('sliders'));
    }

    /**
     * Mettre à jour l'ordre des sliders
     */
    public function updateOrder(Request $request)
    {
        $request->validate(['positions' => 'required|array']);

        try {
            $positions = $request->positions;
            $uniquePositions = array_unique(array_values($positions));

            if (count($positions) !== count($uniquePositions)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Les positions doivent être uniques.'
                ], 400);
            }

            foreach ($positions as $sliderId => $position) {
                Slider::where('id', $sliderId)->update(['position' => $position]);
            }

            return response()->json([
                'success' => true,
                'message' => 'L\'ordre des sliders a été mis à jour avec succès.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('admin.sliders.create');
    }

    /**
     * Créer un nouveau slider
     */
    public function store(Request $request)
    {
        Log::info("Début de la création d'un slider", [
            'request_data' => $request->all(),
            'has_file' => $request->hasFile('image')
        ]);

        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // 1. Créer le slider
            $slider = Slider::create([
                'title' => $request->title,
                'is_active' => $request->has('is_active'),
                'position' => Slider::count()
            ]);

            // 2. Traiter l'image
            if ($request->hasFile('image')) {
                $this->processImageUpload($request->file('image'), $slider);
            }

            return redirect()->route('admin.sliders.index')
                ->with('success', 'Slider créé avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la création: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Une erreur est survenue: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Slider $slider)
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    /**
     * Mettre à jour un slider existant
     */
    public function update(Request $request, Slider $slider)
    {
        Log::info("Début de la mise à jour du slider", [
            'slider_id' => $slider->id,
            'request_data' => $request->all(),
            'has_file' => $request->hasFile('image')
        ]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'position' => 'required|integer|min:1',
            'is_active' => 'sometimes|boolean',
        ]);

        try {
            // Mettre à jour les informations de base
            $updateData = [
                'title' => $validated['title'],
                'is_active' => $request->input('is_active', false),
            ];

            // Gestion de la position
            $newPosition = $request->position - 1;
            if ($newPosition !== $slider->position) {
                $this->resolvePositionConflict($slider, $newPosition);
                $updateData['position'] = $newPosition;
            }

            $slider->update($updateData);

            // Gestion de l'image
            if ($request->hasFile('image')) {
                $this->processImageUpload($request->file('image'), $slider, true);
            }

            return redirect()->route('admin.sliders.index')
                ->with('success', 'Slider mis à jour avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la mise à jour: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'slider_id' => $slider->id
            ]);
            return back()->with('error', 'Une erreur est survenue: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Méthode unifiée pour traiter les images (création et mise à jour)
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param \App\Models\Slider $slider
     * @param bool $isUpdate
     * @throws \Exception
     */
    protected function processImageUpload($file, $slider, $isUpdate = false)
    {
        Log::info("Traitement de l'image", [
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'is_valid' => $file->isValid(),
            'slider_id' => $slider->id,
            'is_update' => $isUpdate
        ]);

        // Vérification complète de la validité du fichier
        if (!$file->isValid()) {
            throw new \Exception("Fichier invalide: " . $file->getError());
        }

        // Supprimer l'ancienne image si c'est une mise à jour
        if ($isUpdate) {
            $this->deleteOldSliderImage($slider);
        }

        // Générer un nom de fichier unique
        $fileName = $this->generateUniqueFileName($file->getClientOriginalName());
        $storagePath = 'sliders/' . $fileName;
        $physicalPath = storage_path('app/public/' . $storagePath);

        // Vérification et préparation complète du dossier de stockage
        $this->prepareStorageDirectory();

        // Vérification supplémentaire de l'environnement de stockage
        $this->verifyStorageEnvironment();

        // Stocker le fichier avec méthode alternative plus fiable
        $this->storeFileSafely($file, $storagePath, $physicalPath);

        // Traiter l'image avec Intervention (si disponible)
        $this->processImageWithIntervention($physicalPath);

        // Enregistrer dans la base de données
        $this->saveImageRecord($slider, $storagePath, $fileName, $isUpdate, $file);
    }

    /**
     * Supprimer l'ancienne image d'un slider
     */
    protected function deleteOldSliderImage($slider)
    {
        if ($slider->mainImage()) {
            $oldImage = $slider->mainImage();
            try {
                $path = str_replace('storage/', '', $oldImage->path);
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                    Log::info("Ancienne image supprimée du stockage", ['path' => $path]);
                }
                $oldImage->delete();
                Log::info("Ancienne image supprimée de la base de données");
            } catch (\Exception $e) {
                Log::error("Erreur lors de la suppression de l'ancienne image: " . $e->getMessage());
                throw $e;
            }
        }
    }

    /**
     * Générer un nom de fichier unique
     */
    protected function generateUniqueFileName($originalName)
    {
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $safeName = Str::slug($baseName) . '.' . $extension;
        $counter = 1;

        while (Storage::disk('public')->exists('sliders/' . $safeName)) {
            $safeName = Str::slug($baseName) . '-' . $counter . '.' . $extension;
            $counter++;
        }

        return $safeName;
    }

    /**
     * Préparer le dossier de stockage avec vérifications complètes
     */
    protected function prepareStorageDirectory()
    {
        $directory = storage_path('app/public/sliders');

        // Vérifier si le dossier existe
        if (!file_exists($directory)) {
            Log::info("Création du dossier de stockage: " . $directory);
            if (!mkdir($directory, 0775, true)) {
                throw new \Exception("Impossible de créer le dossier de stockage: " . $directory);
            }
        }

        // Vérifier les permissions
        if (!is_writable($directory)) {
            $currentPerms = substr(sprintf('%o', fileperms($directory)), -4);
            throw new \Exception("Le dossier de stockage n'est pas accessible en écriture (permissions actuelles: $currentPerms): " . $directory);
        }

        Log::info("Dossier de stockage prêt", ['directory' => $directory]);
    }

    /**
     * Vérification complète de l'environnement de stockage
     */
    protected function verifyStorageEnvironment()
    {
        // Vérifier que le disque public est configuré
        $disks = config('filesystems.disks');
        if (!isset($disks['public'])) {
            throw new \Exception("Le disque 'public' n'est pas configuré dans filesystems.php");
        }

        // Vérifier que le root du disque public existe
        $root = $disks['public']['root'];
        if (!file_exists($root)) {
            throw new \Exception("Le dossier root du disque public n'existe pas: " . $root);
        }

        // Vérifier que le dossier est accessible en écriture
        if (!is_writable($root)) {
            $currentPerms = substr(sprintf('%o', fileperms($root)), -4);
            throw new \Exception("Le dossier root du disque public n'est pas accessible en écriture (permissions: $currentPerms): " . $root);
        }

        // Vérifier que le lien symbolique existe
        $link = public_path('storage');
        if (!file_exists($link)) {
            throw new \Exception("Le lien symbolique public/storage n'existe pas. Exécutez 'php artisan storage:link'");
        }

        // Vérifier que le lien pointe vers le bon endroit
        $target = readlink($link);
        if ($target !== $root) {
            throw new \Exception("Le lien symbolique pointe vers le mauvais endroit. Target: $target, Expected: $root");
        }

        Log::info("Environnement de stockage vérifié avec succès");
    }

    /**
     * Stocker le fichier avec méthode alternative plus fiable
     */
    protected function storeFileSafely($file, $storagePath, $physicalPath)
    {
        // Utiliser un chemin temporaire absolu
        $tempPath = sys_get_temp_dir() . '/' . uniqid() . '_' . $file->getClientOriginalName();

        // Déplacer le fichier vers un emplacement temporaire d'abord
        if (!$file->move(sys_get_temp_dir(), basename($tempPath))) {
            throw new \Exception("Impossible de déplacer le fichier vers l'emplacement temporaire");
        }

        // Vérifier que le fichier temporaire existe
        if (!file_exists($tempPath)) {
            throw new \Exception("Le fichier temporaire n'existe pas après le déplacement");
        }

        // Créer le dossier de destination si nécessaire
        $directory = dirname($physicalPath);
        if (!file_exists($directory)) {
            if (!mkdir($directory, 0775, true)) {
                throw new \Exception("Impossible de créer le dossier de destination: " . $directory);
            }
        }

        // Copier le fichier vers sa destination finale
        if (!copy($tempPath, $physicalPath)) {
            throw new \Exception("Impossible de copier le fichier vers sa destination finale");
        }

        // Supprimer le fichier temporaire
        unlink($tempPath);

        // Vérifier que le fichier final existe et a la bonne taille
        if (!file_exists($physicalPath) || filesize($physicalPath) === 0) {
            throw new \Exception("Le fichier final n'existe pas ou est vide après la copie");
        }

        Log::info("Fichier stocké avec succès", [
            'physical_path' => $physicalPath,
            'size' => filesize($physicalPath)
        ]);
    }

    /**
     * Traiter l'image avec Intervention Image
     */
    protected function processImageWithIntervention($physicalPath)
    {
        try {
            if (class_exists('Intervention\Image\Facades\Image') && file_exists($physicalPath)) {
                $img = Image::make($physicalPath);
                $img->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $img->save($physicalPath, 70);
                Log::info("Image traitée avec Intervention Image");
            }
        } catch (\Exception $e) {
            Log::warning("Erreur lors du traitement de l'image: " . $e->getMessage());
            // On continue même si le traitement échoue
        }
    }

    /**
     * Enregistrer l'image dans la base de données
     */
    protected function saveImageRecord($slider, $storagePath, $fileName, $isUpdate, $file)
    {
        // Supprimer les anciennes images si c'est une mise à jour
        if ($isUpdate) {
            $slider->images()->where('type', 'original')->delete();
        }

        $extension = $file->getClientOriginalExtension();

        $image = $slider->images()->create([
            'path' => $storagePath,
            'format' => $extension,
            'type' => 'original',
            'position' => 0,
            'imageable_type' => 'App\Models\Slider',
        ]);

        Log::info("Image enregistrée dans la base de données", [
            'image_id' => $image->id,
            'path' => $image->path
        ]);

        return $image;
    }

    /**
     * Résoudre les conflits de position
     */
    protected function resolvePositionConflict($slider, $newPosition)
    {
        $existingSlider = Slider::where('position', $newPosition)
            ->where('id', '!=', $slider->id)
            ->first();

        if ($existingSlider) {
            if ($newPosition < $slider->position) {
                Slider::where('position', '>=', $newPosition)
                    ->where('position', '<', $slider->position)
                    ->where('id', '!=', $slider->id)
                    ->increment('position');
            } else {
                Slider::where('position', '<=', $newPosition)
                    ->where('position', '>', $slider->position)
                    ->where('id', '!=', $slider->id)
                    ->decrement('position');
            }
        }
    }

    /**
     * Corriger les positions des sliders
     */
    public function fixPositions()
    {
        $sliders = Slider::orderBy('position')->get();

        foreach ($sliders as $index => $slider) {
            if ($slider->position != $index) {
                $slider->update(['position' => $index]);
            }
        }

        return redirect()->back()->with('success', 'Les positions des sliders ont été corrigées.');
    }

    /**
     * Vérifier si une position est disponible
     */
    public function checkPosition(Request $request)
    {
        $request->validate([
            'position' => 'required|integer|min:1',
            'slider_id' => 'required|integer|exists:sliders,id'
        ]);

        $position = $request->position - 1;
        $sliderId = $request->slider_id;

        $exists = Slider::where('position', $position)
            ->where('id', '!=', $sliderId)
            ->exists();

        return response()->json(['available' => !$exists]);
    }

    /**
     * Supprimer un slider
     */
    public function destroy(Slider $slider)
    {
        try {
            foreach ($slider->images as $image) {
                $image->delete();
            }
            $slider->delete();

            return redirect()->route('admin.sliders.index')
                ->with('success', 'Slider supprimé avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la suppression: " . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }

    /**
     * Afficher un slider spécifique
     */
    public function show(Slider $slider)
    {
        $this->authorize('view', $slider);
        return view('admin.sliders.show', compact('slider'));
    }
}
