<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'total_price',
        'status',
        'payment_proof',
        'payment_method',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            0 => 'Pending',
            1 => 'Processing',
            2 => 'Shipped',
            3 => 'Completed',
            4 => 'Cancelled',
        };
    }

    public function getPaymentMethodLabelAttribut(){
        return match ($this->payment_method) {
            '0' => 'Cash',
            '1' => 'Transfer',
        };
    }

    public function transaction_items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
