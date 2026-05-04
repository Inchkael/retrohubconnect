<?php
// app/Models/Forum.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    protected $fillable = ['name', 'description', 'category_id', 'user_id'];

    public function topics()
    {
        return $this->hasMany(ForumTopic::class, 'forum_id');
    }

    public function category()
    {
        return $this->belongsTo(ForumCategory::class, 'category_id');
    }

    public function replies()
    {
        return $this->hasManyThrough(
            \App\Models\ForumReply::class,
            \App\Models\ForumTopic::class,
            'forum_id', // Clé étrangère sur la table topics
            'topic_id', // Clé étrangère sur la table replies
            'id',       // Clé locale sur la table forums
            'id'        // Clé locale sur la table topics
        );
    }
}
