<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeRole extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug'
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function employee()
    {
        return $this
            ->hasMany('App\Models\Employee', 'role_id', 'id')
            ->select(['id', 'role_id', 'name', 'image_path'])
            ->limit(2);
    }

    public function acls() {
        return $this->hasMany('App\Models\ACL', 'role_id', 'id');
    }
}
