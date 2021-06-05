<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit'
    ];

    public function menu(): HasOne
    {
        return $this->hasOne('App\Models\Menu');
    }

    public function out(): HasMany
    {
        return $this->hasMany('App\Models\OutgoingStock');
    }

    public function in(): HasMany
    {
        return $this->hasMany('App\Models\IncomingStock');
    }
}
