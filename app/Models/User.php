<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;

/**
 * Modèle User
 *
 * Représente un utilisateur dans le système, avec des rôles (ADMIN, USER, PROVIDER)
 * et des champs supplémentaires pour les informations personnelles/professionnelles.
 *
 * Ce modèle étend Authenticatable pour bénéficier des fonctionnalités d'authentification
 * et utilise plusieurs traits pour ajouter des fonctionnalités :
 * - HasFactory : pour la génération de modèles en usine (factories)
 * - Notifiable : pour les notifications
 * - HasApiTokens : pour la gestion des tokens d'API (Sanctum)
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var list<string>
     */
    protected $fillable = [
        'last_name',          // Nom de famille
        'first_name',         // Prénom
        'email',              // Adresse email
        'password',           // Mot de passe (hashé)
        'address',            // Adresse postale (nullable)
        'vat_number',         // Numéro de TVA (nullable, pour les prestataires)
        'mobile_phone',       // Numéro de téléphone mobile (nullable)
        'login_attempts',     // Nombre de tentatives de connexion échouées (par défaut: 0)
        'language',           // Langue préférée (par défaut: 'fr')
        'website',            // Site web personnel/professionnel (nullable)
        'role',               // Rôle: ADMIN, USER, PROVIDER (par défaut: USER)
        'is_banned',          // Statut de bannissement (par défaut: false)
        'is_confirmed',       // Statut de confirmation d'inscription (par défaut: false)
        'registration_date',  // Date d'inscription (automatiquement définie à la date courante)
        'provider',           // Indique le prestataire
        'provider_id',        // ID externe du prestataire
        'confirmation_token',
        'confirmed_at',
        'password_reset_token',
        'token_created_at',
        'avatar',
        'logo',
        'description',
        'latitude',
        'longitude'
    ];

    /**
     * Les attributs qui doivent être cachés lors de la sérialisation.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_banned' => 'boolean',
            'is_confirmed' => 'boolean',
            'registration_date' => 'datetime',
        ];
    }

    // --- MÉTHODES UTILITAIRES ---

    /**
     * Vérifie si l'utilisateur est un administrateur.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'ADMIN';
    }

    /**
     * Vérifie si l'utilisateur est un prestataire.
     *
     * @return bool
     */
    public function isProvider(): bool
    {
        return $this->role === 'PROVIDER';
    }

    /**
     * Vérifie si l'utilisateur est banni.
     *
     * @return bool
     */
    public function isBanned(): bool
    {
        return $this->is_banned;
    }

    /**
     * Vérifie si l'inscription de l'utilisateur est confirmée.
     *
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->is_confirmed;
    }

    /**
     * Retourne le nom complet de l'utilisateur (last_name + first_name).
     *
     * @return string
     */
    public function getFullName(): string
    {
        return "{$this->last_name} {$this->first_name}";
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Relation avec les catégories de services (pour les prestataires).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function serviceCategories()
    {
        return $this->belongsToMany(ServiceCategory::class, 'service_category_user', 'user_id', 'service_category_id')->withTimestamps();
    }


    /**
     * Génère un token de confirmation aléatoire.
     */
    public function generateConfirmationToken(): string
    {
        $this->confirmation_token = Str::random(60);
        $this->save();
        return $this->confirmation_token;
    }

    /**
     * Valide l'email de l'utilisateur.
     */
    public function confirmEmail(): void
    {
        $this->is_confirmed = true;
        $this->email_verified_at = now();
        $this->confirmation_token = null;
        $this->confirmed_at = now();
        $this->save();
    }


    /**
     * Récupère l'URL du logo.
     *
     * @return string|null
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? Storage::url('logos/' . $this->logo) : null;
    }

    public function getPhotosAttribute($value): array
    {
        $photos = json_decode($value, true) ?: [];
        return array_map(function($photo) {
            return [
                'filename' => $photo,
                'url' => Storage::url('photos/' . $photo)
            ];
        }, $photos);
    }

    /**
     * Récupère l'URL de l'avatar ou du logo selon le rôle
     */
    public function getProfileImageUrlAttribute(): ?string
    {
        if ($this->role === 'PROVIDER' && $this->logo) {
            return Storage::url('logos/' . $this->logo);
        } elseif ($this->avatar) {
            return Storage::url('avatars/' . $this->avatar);
        }
        return null;
    }

    /**
     * Récupère le chemin de l'avatar
     */
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? Storage::url('avatars/' . $this->avatar) : null;
    }


    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    /**
     * Vérifie si l'utilisateur est un utilisateur standard.
     *
     * @return bool
     */
    public function isUser(): bool
    {
        return $this->role === 'USER';
    }


    /**
     * Accessor pour vérifier si les coordonnées sont valides
     */
    public function getHasValidCoordinatesAttribute()
    {
        return $this->latitude && $this->longitude;
    }

    public function commentairesPrestataire()
    {
        return $this->hasMany(Commentaire::class, 'PrestataireID');
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class, 'PrestataireID');
    }

    /**
     * Vérifie si le compte est bloqué
     */
    public function isLocked()
    {
        return $this->login_attempts >= 4;
    }

    /**
     * Incrémente les tentatives de connexion
     */
    public function incrementLoginAttempts()
    {
        $this->login_attempts++;
        $this->save();
    }

    /**
     * Réinitialise les tentatives de connexion
     */
    public function resetLoginAttempts()
    {
        $this->login_attempts = 0;
        $this->save();
    }

    public function unreadMessages()
    {
        return $this->hasMany(Message::class, 'recipient_id')
            ->where('is_read', false)
            ->where('is_draft', false)
            ->where('is_abuse_report', false);
    }





}
