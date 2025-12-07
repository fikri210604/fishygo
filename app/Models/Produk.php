<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Produk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'produk';
    protected $primaryKey = 'produk_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'produk_id',
        'slug',
        'kode_produk',
        'nama_produk',
        'kategori_produk_id',
        'jenis_ikan_id',
        'harga',
        'harga_promo',
        'promo_mulai',
        'promo_selesai',
        'deskripsi',
        'satuan',
        'stok',
        'berat_gram',
        'kadaluarsa',
        'rating_avg',
        'rating_count',
        'aktif',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'harga_promo' => 'decimal:2',
        'promo_mulai' => 'datetime',
        'promo_selesai' => 'datetime',
        'kadaluarsa' => 'date',
        'rating_avg' => 'decimal:2',
        'rating_count' => 'integer',
        'stok' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
            if (empty($model->slug) && !empty($model->nama_produk)) {
                $model->slug = Str::slug($model->nama_produk) . '-' . Str::lower(Str::random(6));
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('nama_produk')) { 
                $model->slug = Str::slug($model->nama_produk) . '-' . Str::lower(Str::random(6));
            }
        });
    }


    // Relasi
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_produk_id');
    }

    public function jenisIkan(): BelongsTo
    {
        return $this->belongsTo(JenisIkan::class, 'jenis_ikan_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ProdukFoto::class, 'produk_id');
    }

    public function primaryPhoto()
    {
        return $this->hasOne(ProdukFoto::class, 'produk_id')->where('is_primary', true)->orderBy('urutan');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ReviewProduk::class, 'produk_id', 'produk_id');
    }

    // Akses nilai gambar utama dari relasi foto (fallback ke kolom lama jika ada)
    public function getGambarProdukAttribute($value)
    {
        // Prioritaskan relasi yang sudah di-load
        if ($this->relationLoaded('primaryPhoto') && $this->primaryPhoto) {
            return $this->primaryPhoto->path;
        }
        if ($this->relationLoaded('photos') && $this->photos->count() > 0) {
            $primary = $this->photos->firstWhere('is_primary', true);
            if ($primary) { return $primary->path; }
            return $this->photos->sortBy(['is_primary','desc'], ['urutan','asc'])->first()?->path ?? $value;
        }
        $path = $this->primaryPhoto()->value('path');
        if ($path) { return $path; }
        $first = $this->photos()->orderByDesc('is_primary')->orderBy('urutan')->value('path');
        return $first ?: $value;
    }

    public function recalcRating(): void
    {
        $rows = $this->reviews()
            ->whereNull('deleted_at')
            ->whereNotNull('rating')
            ->orderBy('pengguna_id')
            ->orderBy('created_at') 
            ->get(['pengguna_id','rating','created_at']);

        $perUser = [];
        foreach ($rows as $r) {
            if (!array_key_exists($r->pengguna_id, $perUser)) {
                $perUser[$r->pengguna_id] = (int) $r->rating;
            }
        }
        $cnt = count($perUser);
        $avg = $cnt ? array_sum($perUser) / $cnt : 0;
        $this->rating_count = $cnt;
        $this->rating_avg = round((float) $avg, 2);
        $this->save();
    }

    // Helper harga efektif (memakai promo jika aktif & dalam rentang waktu)
    public function hargaEfektif(): string
    {
        if ($this->isPromoAktif()) {
            return (string) $this->harga_promo;
        }
        return (string) $this->harga;
    }

    public function isPromoAktif(): bool
    {
        if (is_null($this->harga_promo)) {
            return false;
        }
        $now = now();
        $mulai = $this->promo_mulai ?: $now->copy()->subYear();
        $selesai = $this->promo_selesai ?: $now->copy()->addYear();
        return $now->between($mulai, $selesai);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('aktif', '1');
    }

    public function scopeCari($query, ?string $q)
    {
        if (!$q)
            return $query;
        return $query->where(function ($w) use ($q) {
            $w->where('nama_produk', 'like', "%{$q}%")
                ->orWhere('kode_produk', 'like', "%{$q}%");
        });
    }
}
