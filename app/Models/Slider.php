<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Modèle Slider
 *
 * Ce modèle représente un slider (bannière glissante) dans l'application.
 * Un slider peut contenir plusieurs images et être activé/désactivé.
 * Les sliders sont généralement utilisés pour mettre en avant des contenus
 * importants sur la page d'accueil ou d'autres pages clés.
 *
 * Fonctionnalités principales :
 * - Gestion des images associées (relation polymorphique)
 * - Tri des images par position
 * - Récupération des sliders actifs
 * - Mise à jour des positions des images
 * - Suppression automatique des fichiers lors de la suppression d'une image
 */
class Slider extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'is_active', 'position'];

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable')->orderBy('position');
    }

    public function mainImage()
    {
        return $this->images()->where('type', 'original')->first();
    }

    /**
     * Récupère toutes les images du slider triées par position
     */
    public function getSortedImages()
    {
        return $this->images()->orderBy('position')->get();
    }

    // Méthode statique pour récupérer les sliders actifs triés par position
    public static function getActiveOrdered()
    {
        return self::where('is_active', true)
            ->orderBy('position', 'asc')
            ->with(['images' => function($query) {
                $query->where('type', 'original')->orderBy('position');
            }])
            ->get();
    }

    /**
     * Met à jour la position des images
     */
    public function updateImagePositions(array $imageOrders)
    {
        // Mettre à jour les positions
        foreach ($imageOrders as $imageId => $position) {
            $this->images()->where('id', $imageId)->update(['position' => $position]);
        }

        // Réorganiser pour éviter les trous
        $images = $this->images()->orderBy('position')->get();
        foreach ($images as $index => $image) {
            $image->update(['position' => $index]);
        }
    }

    /**
     * Obtenir le modèle propriétaire de l'image
     */
    public function imageable()
    {
        return $this->morphTo();
    }

    /**
     * Génère l'URL complète de l'image
     */
    public function getUrlAttribute()
    {
        // Vérifie si le chemin commence déjà par 'storage/'
        if (str_starts_with($this->path, 'storage/')) {
            return asset($this->path);
        }
        return asset('storage/' . $this->path);
    }

    /**
     * Supprime le fichier physique de l'image
     */
    public function deleteFile()
    {
        if (Storage::disk('public')->exists(str_replace('storage/', '', $this->path))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $this->path));
        }
    }

    public function getTranslatedTextAttribute()
    {
        return __("sliders.slider_{$this->id}"); // Ex: sliders.slider_1
    }

    protected static function booted()
    {
        static::deleting(function ($image) {
            $image->deleteFile();
        });
    }
}
