<?php

namespace App\Services;

use App\Models\Produto;
use App\Models\Fornecedor;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function registerStockEntry(array $data)
    {
        return DB::transaction(function () use ($data) {
            $product = Produto::findOrFail($data['produto_id']);
            $supplier = Fornecedor::findOrFail($data['fornecedor_id']);
            
            $newPurchasePrice = $data['purchase_price'];
            $quantity = $data['quantity'];

            $markupPercent = $product->markup ?? 0;

            // 2. Calculamos o novo preço de venda
            // Fórmula: Custo + (Custo * (Markup / 100))
            // Ex: 2070 + (2070 * 0.05) = 2173.50
            
            $newSalePrice = $newPurchasePrice * (1 + ($markupPercent / 100));

            // 3. Atualizamos o produto com os novos valores
            $product->purchase_price = $newPurchasePrice;
            $product->sale_price = $newSalePrice;
            $product->stock_quantity += $quantity;
            $product->save();

            // --- FIM DO CÁLCULO ---

            // Registar o histórico de movimento
            StockMovement::create([
                'produto_id' => $product->id,
                'type'       => 'in',
                'quantity'   => $quantity,
                'reason'     => "Entrada de Stock: Fornecedor {$supplier->name}",
                'user_id'    => auth()->id() ?? 1,
            ]);

            // Gerir dívida com fornecedor se for a prazo
            if ($data['payment_status'] === 'unpaid') {
                $totalPurchase = $quantity * $newPurchasePrice;
                $supplier->increment('balance_to_pay', $totalPurchase);
            }

            return $product;
        });
    }
}