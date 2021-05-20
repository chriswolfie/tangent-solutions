<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'user_id', 'category_id'];

    public function users()
    {
        return $this->belongsTo(Users::class, 'user_id', 'id');
    }

    public function categories()
    {
        return $this->belongsTo(Categories::class, 'category_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comments::class, 'post_id', 'id');
    }
}
