<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function orders(){
        return $this->belongsToMany(Product::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }
}
