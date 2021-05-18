<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Menu extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    protected $fillable = [
        'name',
        'description',
        'price',
        'unit',
        'is_available',
        'menu_type',
        'image_path',
        'ingredient_id',
        'serving_size'
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo('App\Models\Ingredient');
    }
}
