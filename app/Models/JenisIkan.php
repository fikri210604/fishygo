<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisIkan extends Model
{
    use HasFactory;

    protected $table = 'jenis_ikan';
    protected $primaryKey = 'jenis_ikan_id';

    protected $fillable = [
        'jenis_ikan',
        'gambar_jenis_ikan',
    ];

    public function produk()
    {
        return $this->hasMany(Produk::class);
    }
}
