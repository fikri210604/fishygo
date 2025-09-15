<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    use HasFactory;

    protected $primaryKey = 'code';   // primary key pakai code
    public $incrementing = false;     // bukan auto increment
    protected $keyType = 'string';    // tipe data string

    protected $fillable = ['code', 'name', 'district_code'];

    public function district()
    {
        return $this->belongsTo(District::class, 'district_code', 'code');
    }

    public function regency()
    {
        return $this->hasOneThrough(
            Regency::class,
            District::class,
            'code',         // PK di districts
            'code',         // PK di regencies
            'district_code',// FK di villages → districts
            'regency_code'  // FK di districts → regencies
        );
    }

    public function province()
    {
        return $this->hasOneThrough(
            Province::class,
            Regency::class,
            'code',          // PK di regencies
            'code',          // PK di provinces
            'regency_code',  // FK di districts → regencies
            'province_code'  // FK di regencies → provinces
        );
    }
}
