<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'subtotal',
        'tax',
        'service',
        'grand_total',
        'payment_method',
        'cashier_id',
        'order_id'
    ];

    public function cashier(): BelongsTo
    {
        return $this->belongsTo('App\Models\Employee', 'cashier_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo('App\Models\Order');
    }
}
