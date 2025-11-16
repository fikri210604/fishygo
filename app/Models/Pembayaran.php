<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';
    protected $primaryKey = 'pembayaran_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'pembayaran_id',
        'pesanan_id',
        'gateway', 'channel', 'amount', 'status',
        'reference', 'order_id', 'transaction_id',
        'gateway_payload', 'dibayar_pada', 'expiry_time',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_payload' => 'array',
        'expiry_time' => 'datetime',
        'dibayar_pada' => 'datetime',
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

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id', 'pesanan_id');
    }
}
