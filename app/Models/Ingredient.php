<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingredient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'unit'
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function menu(): HasOne
    {
        return $this->hasOne('App\Models\Menu');
    }
}
