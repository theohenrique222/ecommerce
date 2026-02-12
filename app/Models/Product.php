<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'price'];
    
    public function sales()
    {
        return $this->belongsToMany(Sale::class)
            ->withPivot(['quantity', 'unit_price', 'subtotal']);
    }
}
