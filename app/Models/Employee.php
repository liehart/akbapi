<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Laravel\Scout\Searchable;

class Employee extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'phone',
        'date_join',
        'gender',
        'email',
        'password',
        'role_id',
        'is_disabled'
    ];

    protected $hidden = [
        'deleted_at',
        'password',
        'remember_token',
    ];

    public function role() {
        return $this->belongsTo('App\Models\EmployeeRole');
    }
}
