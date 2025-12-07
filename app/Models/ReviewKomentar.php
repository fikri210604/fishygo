<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ReviewKomentar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'review_komentar';
    protected $primaryKey = 'komentar_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'komentar_id',
        'produk_id',
        'review_id',
        'pengguna_id',
        'komentar',
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

    public function review()
    {
        return $this->belongsTo(ReviewProduk::class, 'review_id', 'review_id');
    }

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'pengguna_id', 'id');
    }
}

