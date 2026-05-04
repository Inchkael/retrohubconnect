<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumCategory extends Model
{
    protected $fillable = ['name', 'description'];

    public function forums()
    {
        return $this->hasMany(Forum::class, 'category_id');
    }
}
