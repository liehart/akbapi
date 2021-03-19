<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionCard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cardholder_name',
        'cardholder_number',
        'cardholder_exp_month',
        'cardholder_exp_year',
        'card_type'
    ];

    protected $hidden = [
        'deleted_at',
    ];
}
