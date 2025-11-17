<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class ReviewProduk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'review_produk';
    protected $primaryKey = 'review_id';
    protected $keyType = 'string';
    
    protected $fillable = [
        'produk_id',
        'pengguna_id',
        'review',
        'rating',
    ];

    protected $casts = [
        'rating' => 'integer',
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

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'pengguna_id', 'id');
    }
}
