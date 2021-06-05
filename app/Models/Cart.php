<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'order_id',
        'menu_id',
        'quantity'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo('App\Models\Order');
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo('App\Models\Menu');
    }
}
