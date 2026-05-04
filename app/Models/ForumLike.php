<?php

namespace App\Models;

// app/Models/ForumLike.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumLike extends Model
{
    protected $fillable = [
        'reply_id',
        'user_id',
    ];

    // Relation avec la réponse
    public function reply()
    {
        return $this->belongsTo(ForumReply::class);
    }

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
