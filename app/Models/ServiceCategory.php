<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle ServiceCategory
 *
 * Ce modèle représente une catégorie de services dans l'application.
 * Il permet de gérer les catégories de services proposés par les prestataires,
 * avec des fonctionnalités comme la validation, la mise en avant et les images associées.
 *
 * Fonctionnalités principales :
 * - Gestion des catégories de services
 * - Relation avec les utilisateurs (prestataires)
 * - Gestion des images associées (relation polymorphique)
 * - Génération de balises HTML pour l'affichage des images
 * - Vérification de la présence d'images
 */
class ServiceCategory extends Model
{
    use HasFactory;

    protected $table = 'service_categories';

    protected $fillable = [
        'name',
        'description',
        'is_highlighted',
        'is_validated',
        'is_monthly',
    ];

    protected $casts = [
        'is_highlighted' => 'boolean',
        'is_validated' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'service_category_user',
            'service_category_id',
            'user_id'
        )->withTimestamps();
    }

    /**
     * Relation polymorphique avec les images
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable')->orderBy('position');
    }

    /**
     * Obtenir les images originales
     */
    public function originalImages()
    {
        return $this->images()->where('type', 'original')->orderBy('position');
    }

    /**
     * Génère la balise picture pour une image
     */
    public function getImagePictureTag($image, $alt = '', $classes = 'img-fluid')
    {
        if (!$image || $image->type !== 'original') {
            return '<img src="' . asset('images/placeholder.jpg') . '" alt="' . e($alt) . '" class="' . e($classes) . '">';
        }

        $baseName = pathinfo($image->path, PATHINFO_FILENAME);
        $baseName = preg_replace('/-original$/', '', $baseName);

        $sizes = [
            '380' => '(max-width: 576px) 380px',
            '540' => '(max-width: 768px) 540px',
            '700' => '700px'
        ];

        $html = '<picture>';

        // Sources dans l'ordre de préférence
        foreach (['avif', 'webp', 'png', 'jpg'] as $format) {
            $html .= '<source type="image/' . e($format) . '" srcset="';
            $srcsetParts = [];

            foreach ([380, 540, 700] as $size) {
                $optimizedImage = $this->images()
                    ->where('type', "{$size}w")
                    ->where('format', $format)
                    ->first();

                if ($optimizedImage) {
                    $srcsetParts[] = $optimizedImage->url . ' ' . $size . 'w';
                }
            }

            if (!empty($srcsetParts)) {
                $html .= implode(', ', $srcsetParts) . '" sizes="' . implode(', ', $sizes) . '">';
            }
        }

        // Fallback avec l'image originale
        $html .= $image->getHtmlTag($alt, $classes);
        $html .= '</picture>';

        return $html;
    }

    /**
     * Vérifie si la catégorie a des images
     */
    public function hasImages()
    {
        return $this->originalImages()->count() > 0;
    }
}
