<?php

// app/Models/ForumReply.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumReply extends Model
{
    protected $fillable = [
        'topic_id',
        'user_id',
        'content',
    ];

    // Relation avec le sujet
    public function topic()
    {
        return $this->belongsTo(ForumTopic::class);
    }

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec les "likes"
    public function likes()
    {
        return $this->hasMany(ForumLike::class, 'reply_id');
    }


}
