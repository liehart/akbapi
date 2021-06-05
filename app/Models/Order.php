<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reservation_id',
        'finish_at',
        'waiter_id',
        'token'
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo('App\Models\Reservation');
    }

    public function details(): HasMany
    {
        return $this->HasMany('App\Models\OrderDetail');
    }

    public function waiter(): BelongsTo
    {
        return $this->belongsTo('App\Models\Employee', 'waiter_id');
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo('App\Models\Table', 'table_number');
    }

    public function transaction(): HasOne
    {
        return $this->hasOne('App\Models\Transaction');
    }
}
