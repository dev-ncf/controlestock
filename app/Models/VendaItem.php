<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendaItem extends Model
{
    //
    protected $fillable = ['venda_id', 'produto_id', 'quantity', 'unit_price', 'cost_price', 'subtotal'];

public function produto() {
    return $this->belongsTo(Produto::class);
}
}
