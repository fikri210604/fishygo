<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisIkan extends Model
{
    use HasFactory;

    protected $table = 'table_jenis_ikan';

    protected $fillable = [
        'jenis_ikan',
        'gambar_jenis_ikan',
    ];
}

