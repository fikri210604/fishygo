<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class Pengiriman extends Model
{
    use HasFactory;

    protected $table = 'pengiriman';
    protected $primaryKey = 'pengiriman_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'pengiriman_id',
        'pesanan_id',
        'kurir_kode',
        'kurir_service',
        'resi',
        'biaya',
        'status',
        'dikemas_pada',
        'dikirim_pada',
        'diterima_pada',
        'assigned_kurir_id',
    ];

    protected $casts = [
        'biaya' => 'decimal:2',
        'dikemas_pada' => 'datetime',
        'dikirim_pada' => 'datetime',
        'diterima_pada' => 'datetime',
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

    public function kurir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_kurir_id', 'id');
    }

    public function getStatusLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getStatusClassAttribute()
    {
        return [
            'siap' => 'badge-ghost',
            'diambil' => 'badge-info',
            'diantar' => 'badge-warning',
            'diterima' => 'badge-success',
            'dikembalikan' => 'badge-error',
        ][$this->status] ?? 'badge-ghost';
    }

    public function getActionButtonsAttribute()
    {
        return match ($this->status) {
            'siap' => ['pickup' => 'Ambil Barang', 'deliver' => 'Mulai Kirim'],
            'diambil' => ['deliver' => 'Mulai Kirim', 'return' => 'Kembalikan'],
            'diantar' => ['complete' => 'Sampai', 'return' => 'Kembalikan'],
            default => [],
        };
    }

    public function getButtonClassAttribute()
    {
        return [
            'pickup' => 'btn btn-xs',
            'deliver' => 'btn btn-xs',
            'complete' => 'btn btn-xs btn-primary',
            'return' => 'btn btn-xs btn-error text-white',
        ];
    }

    public static function statusGroups(): array
    {
        return [
            'siap' => 'Siap',
            'diambil' => 'Diambil',
            'diantar' => 'Diantar',
            'dikembalikan' => 'Dikembalikan',
            'diterima' => 'Diterima',
        ];
    }

    public static function groupCollectionByStatus(Collection $rows): array
    {
        $groups = [];
        foreach (self::statusGroups() as $key => $label) {
            $subset = $rows->where('status', $key);
            if ($subset->count()) {
                $groups[] = ['key' => $key, 'label' => $label, 'rows' => $subset];
            }
        }
        return $groups;
    }

}
