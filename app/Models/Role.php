<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function employee(): HasMany
    {
        return $this
            ->hasMany('App\Models\Employee')
            ->select(['id', 'role_id', 'name', 'image_path'])
            ->limit(2);
    }

    public function acls(): HasMany
    {
        return $this->hasMany('App\Models\ACL');
    }

    public function permission(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Permission', 'role_has_permissions', 'role_id', 'permission_id');
    }
}
