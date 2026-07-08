<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    //
    protected $fillable = ['categoria_id', 'name', 'sku','produto_pai_id','fator_conversao', 'purchase_price', 'sale_price',  'markup','stock_quantity', 'min_stock'];

public function categoria() {
    return $this->belongsTo(Categoria::class);
}

public function stockMovements() {
    return $this->hasMany(StockMovement::class);
}
public function pai() {
    return $this->belongsTo(Produto::class, 'produto_pai_id');
}

public function filhos() {
    return $this->hasMany(Produto::class, 'produto_pai_id');
}
}
