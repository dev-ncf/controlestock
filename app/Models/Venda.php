<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    //

    protected $fillable = ['cliente_id', 'invoice_number', 'date', 'due_date', 'total_amount', 'status'];
protected $casts = ['date' => 'datetime', 'due_date' => 'datetime'];

public function cliente() {
    return $this->belongsTo(Cliente::class);
}

public function items() {
    return $this->hasMany(VendaItem::class);
}

public function pagamentos() {
    return $this->hasMany(Pagamento::class);
}
}
