<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncomingStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'price',
        'ingredient_id',
        'employee_id'
    ];

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo('App\Models\Ingredient');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo('App\Models\Employee');
    }
}
