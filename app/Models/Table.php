<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'table_number';

    protected $casts = [
        'table_number' => 'string',
    ];

    protected $fillable = [
        'table_number',
        'is_empty',
    ];

    protected $hidden = [
        'deleted_at',
    ];
}
