<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    //
    protected $fillable = ['name', 'nif', 'phone', 'credit_limit', 'current_balance'];

public function vendas() {
    return $this->hasMany(Venda::class);
}

// Para ver quanto o cliente ainda deve no total
public function getTotalDebtAttribute() {
    return $this->vendas()->whereIn('status', ['unpaid', 'partial'])->sum('total_amount');
}
}
