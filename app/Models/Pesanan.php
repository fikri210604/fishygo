<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Pesanan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pesanan';
    protected $primaryKey = 'pesanan_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'pesanan_id',
        'kode_pesanan',
        'pengguna_id',
        'alamat_id',
        'status',
        'metode_pembayaran',
        'subtotal', 'ongkir', 'diskon', 'total',
        'catatan',
        'alamat_snapshot',
        'berat_total_gram',
        'payment_due',
        'cancelled_at','cancelled_by_id','cancel_reason','cancel_note',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'ongkir' => 'decimal:2',
        'diskon' => 'decimal:2',
        'total' => 'decimal:2',
        'berat_total_gram' => 'integer',
        'payment_due' => 'datetime',
        'alamat_snapshot' => 'array',
        'cancelled_at' => 'datetime',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    public function alamat(): BelongsTo
    {
        return $this->belongsTo(Alamat::class, 'alamat_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PesananItem::class, 'pesanan_id', 'pesanan_id');
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'pesanan_id', 'pesanan_id');
    }

    public function pengiriman(): HasOne
    {
        return $this->hasOne(Pengiriman::class, 'pesanan_id', 'pesanan_id');
    }
    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by_id');
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeUntukUser($query, string $userId)
    {
        return $query->where('pengguna_id', $userId);
    }
}




