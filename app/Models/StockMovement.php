<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    //
    protected $fillable = ['produto_id', 'type', 'quantity', 'reason', 'user_id'];

public function produto() {
    return $this->belongsTo(Produto::class);
}
public function user()
{
    return $this->belongsTo(User::class);
}
}
