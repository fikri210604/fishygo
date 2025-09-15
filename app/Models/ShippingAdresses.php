<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingAdresses extends Model
{
    use HasFactory;

    protected $table = 'shipping_adresses';

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function province(){
        return $this->belongsTo(Province::class);
    }

    public function regency(){
        return $this->belongsTo(Regency::class);
    }

    public function district(){
        return $this->belongsTo(District::class);
    }

    public function village(){
        return $this->belongsTo(Village::class);
    }


}
