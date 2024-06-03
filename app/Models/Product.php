<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\Casts\Attribute;


class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'img', 
        'rating',
    ];


    // Accessor for ratings (example)
    public function getRatingsAttribute($value)
    {
        return json_decode($value);
    }

    
}
