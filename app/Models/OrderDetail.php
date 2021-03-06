<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'ready_to_serve_at',
        'served_at',
        'order_id',
        'menu_id'
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
