<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'session',
        'table_number',
        'customer_id'
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function customer() {
        return $this->belongsTo('App\Models\Customer')->withTrashed();
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo('App\Models\Table', 'table_number', 'table_number');
    }
}
