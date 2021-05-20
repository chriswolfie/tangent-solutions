<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;

    protected $fillable = ['label'];

    public function posts()
    {
        return $this->hasMany(Posts::class, 'category_id', 'id');
    }
}
