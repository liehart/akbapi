<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'original_file_name',
        's3_file_name',
        'scope',
        'content_type',
        'file_size',
        'path'
    ];

    protected $hidden = [
        'deleted_at',
    ];
}
