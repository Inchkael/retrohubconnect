<?php

// app/Models/ForumTopic.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumTopic extends Model
{
    protected $fillable = [
        'forum_id',
        'user_id',
        'title',
        'content',
        'is_locked',
        'is_pinned',
        'views',
    ];
    protected $casts = [
        'is_locked' => 'boolean',
    ];


    // Relation avec le forum
    public function forum()
    {
        return $this->belongsTo(Forum::class);
    }

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec les réponses
    public function replies()
    {
        return $this->hasMany(ForumReply::class, 'topic_id');
    }
}
