<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriProduk extends Model
{
    use HasFactory;

    protected $table = 'kategori_produk';
    protected $primaryKey = 'kategori_produk_id';

    protected $fillable = [
        'nama_kategori',
        'gambar_kategori',
    ];

    public function produk()
    {
        return $this->hasMany(Produk::class);
    }

}
