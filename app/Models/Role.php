<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'nama',
        'slug',
        'aktif',
    ];

    public $timestamps = true;

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id')->withTimestamps();
    }

    public function givePermission(String $slug)
    {
        $permission = Permission::where('slug', $slug)->first();
        if (!$permission) {
            return;
        }
        $attached = $this->permissions()->where('permissions.id', $permission->id)->exists();
        if (!$attached) {
            $this->permissions()->attach($permission->id);
        }
    }
}
