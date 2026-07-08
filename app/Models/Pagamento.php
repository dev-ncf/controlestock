<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    //

    protected $fillable = ['venda_id', 'amount_paid', 'payment_date', 'payment_method', 'reference'];

public function venda() {
    return $this->belongsTo(Venda::class);
}
}
