<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'path', 'format', 'type', 'position',
        'imageable_id', 'imageable_type',
        'original_name', 'mime_type', 'size'
    ];

    public function imageable()
    {
        return $this->morphTo();
    }

    /**
     * Génère l'URL complète de l'image
     * Suppose que le chemin est stocké sans le préfixe 'storage/'
     */
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->path);
    }

    /**
     * Vérifie si le fichier physique existe
     */
    public function exists()
    {
        return Storage::disk('public')->exists($this->path);
    }

    /**
     * Supprime le fichier physique de l'image
     */
    public function deleteFile()
    {
        if ($this->exists()) {
            Storage::disk('public')->delete($this->path);
        }
    }

    protected static function booted()
    {
        static::deleting(function ($image) {
            $image->deleteFile();
        });
    }
}
