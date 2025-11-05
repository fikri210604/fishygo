<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Alamat extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'alamat';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'pengguna_id',
        'label_alamat',
        'penerima',
        'no_telp_penerima',
        'alamat_lengkap',
        'province_id','province_name',
        'regency_id','regency_name',
        'district_id','district_name',
        'village_id','village_name',
        'kode_pos',
    ];

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }
}
