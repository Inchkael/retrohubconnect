<?php
// app/Models/Review.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reviewable_id',    // Peut être item_id OU reply_id
        'reviewable_type',  // Peut être "App\Models\Item" OU "App\Models\Reply"
        'rating',           // Null pour les signalements de commentaires
        'comment',          // Contenu de l'avis OU commentaire du signalement
        'is_helpful',
        'is_reported',
        'report_reason',
        'report_comment'
    ];

    // Relation polymorphique
    public function reviewable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Pour maintenir la compatibilité avec l'ancien code
    public function item()
    {
        return $this->belongsTo(Item::class, 'reviewable_id')
            ->where('reviewable_type', 'App\Models\Item');
    }

    // Nouvelle relation pour les replies
    public function reply()
    {
        return $this->belongsTo(Reply::class, 'reviewable_id')
            ->where('reviewable_type', 'App\Models\Reply');
    }
}
