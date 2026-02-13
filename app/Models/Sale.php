<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'seller_id',
        'client_id',
        'total',
        'status',
        'duo_date'
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'sale_product')
            ->withPivot(['quantity', 'unit_price', 'subtotal']);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
