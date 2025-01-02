<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'shipping_price',
        'shipping_status',
        'total_price',
        'status',
        'resi',
    ];

    public function user(){
        return $this->belongsTo( User::class, 'user_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
