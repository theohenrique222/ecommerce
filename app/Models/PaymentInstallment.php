<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentInstallment extends Model
{
    protected $fillable = [
        'payment_id',
        'installment_number',
        'amount',
        'due_date',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
