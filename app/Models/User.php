<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany as EloquentBelongsToMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'pengguna';
    public $incrementing = false;
    protected $keyType = 'string';

    public const ROLE_ADMIN = 'admin';
    public const ROLE_KURIR = 'kurir';
    public const ROLE_USER = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'username',
        'email',
        'password',
        'email_verified_at',
        'role_slug',
        'address',
        'phone',
        'nomor_telepon',
        'google_id',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'pengguna_role', 'pengguna_id', 'role_id')->withTimestamps();
    }

    /**
     * Mengecek apakah pengguna memiliki permission tertentu via roles.
     */
    public function hasPermission(string $slug): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        // Cek permission aktif melalui relasi roles -> permissions
        return $this->roles()
            ->whereHas('permissions', function ($q) use ($slug) {
                $q->where('slug', $slug)->where('aktif', '1');
            })
            ->exists();
    }

    public function hasRole(string $slug): bool
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles->contains(fn ($r) => $r->slug === $slug);
        }
        // Fallback to single role column for backward compatibility
        if ($this->role_slug === $slug) {
            return true;
        }
        return $this->roles()->where('slug', $slug)->exists();
    }

    public function assignRole(string $slug): void
    {
        $role = Role::query()->where('slug', $slug)->first();
        if (! $role) { return; }
        $attached = $this->roles()->where('roles.id', $role->id)->exists();
        if (! $attached) {
            $this->roles()->attach($role->id);
        }

        if (! $this->role_slug) {
            $this->role_slug = $slug;
            $this->save();
        }
    }

    public function roleKey(): string
    {
        return $this->role_slug ?: self::ROLE_USER;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isKurir(): bool
    {
        return $this->hasRole('kurir');
    }

    public function isUser(): bool
    {
        return $this->hasRole('user');
    }

    public function dashboardRoute(): string
    {
        if ($this->isAdmin()) { return 'admin.dashboard'; }
        if ($this->isKurir()) { return 'kurir.dashboard'; }
        return 'dashboard';
    }

    public static function defaultRoleSlug(): string
    {
        return self::ROLE_USER;
    }

    public function alamats(): HasMany
    {
        return $this->hasMany(Alamat::class, 'pengguna_id');
    }

    // Ada alamat utama lengkap sesuai tabel alamat
    public function hasCompleteAddress(): bool
    {
        return $this->alamats()
            ->whereNotNull('province_id')->where('province_id', '!=', '')
            ->whereNotNull('regency_id')->where('regency_id', '!=', '')
            ->whereNotNull('district_id')->where('district_id', '!=', '')
            ->whereNotNull('village_id')->where('village_id', '!=', '')
            ->whereNotNull('alamat_lengkap')->where('alamat_lengkap', '!=', '')
            ->exists();
    }

    // Profil lengkap jika nomor HP terisi dan punya alamat lengkap
    public function isProfileComplete(): bool
    {
        return filled($this->phone) && $this->hasCompleteAddress();
    }
}
