<?php

namespace App\Services;

use App\Models\Venda;
use App\Models\Produto;
use App\Models\Cliente;
use App\Models\Pagamento;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class VendaService
{
    public function createInvoice(array $data)
    {
        return DB::transaction(function () use ($data) {
            $cliente = Cliente::findOrFail($data['cliente_id']);
            
            // 1. Criar a Venda
            $venda = Venda::create([
                'cliente_id'     => $cliente->id,
                'invoice_number' => $this->generateInvoiceNumber(),
                'date'           => now(),
                'due_date'       => $data['due_date'] ?? now()->addDays(30),
                'total_amount'   => 0,
                'status'         => $data['status'],
            ]);

            $totalVenda = 0;

            // 2. Processar Itens e Stock
            
            foreach ($data['items'] as $item) {
                $produto = Produto::findOrFail($item['produto_id']);
                
                // --- LÓGICA DE DESMEMBRAMENTO DE CAIXAS ---
                if ($produto->produto_pai_id && $produto->stock_quantity < $item['quantity']) {
                    $pai = Produto::find($produto->produto_pai_id);
                    
                    if ($pai && $pai->stock_quantity > 0) {
                        $fator = (int) $produto->fator_conversao;
                        $falta = $item['quantity'] - $produto->stock_quantity; 
                        
                       
                        $caixasParaAbrir = ceil($falta / $fator);

                       
                        if ($pai->stock_quantity < $caixasParaAbrir) {
                            $caixasParaAbrir = $pai->stock_quantity;
                        }

                        if ($caixasParaAbrir > 0) {
                            // 1. Decrementa a quantidade exata de caixas do Pai
                            $pai->decrement('stock_quantity', $caixasParaAbrir);

                            // 2. Incrementa o stock do Filho (Caixas * Fator)
                            $unidadesAdicionadas = $caixasParaAbrir * $fator;
                            $produto->increment('stock_quantity', $unidadesAdicionadas);

                            // 3. Regista o movimento da quebra
                            StockMovement::create([
                                'produto_id' => $pai->id,
                                'type'       => 'out',
                                'quantity'   => $caixasParaAbrir,
                                'reason'     => "Quebra de {$caixasParaAbrir} caixa(s): Abastecimento de avulso ({$produto->name})",
                                'user_id'    => auth()->id() ?? 1,
                            ]);

                            // Atualiza a instância para processar a venda com o novo stock
                            $produto->refresh();
                        }
                    }
                }
                // --- FIM DA LÓGICA DE DESMEMBRAMENTO ---

                // Verificação de segurança final
                if ($produto->stock_quantity < $item['quantity']) {
                    throw new \Exception("Stock insuficiente para: {$produto->name}. Disponível: {$produto->stock_quantity}");
                }



                $subtotal = $item['quantity'] * $item['unit_price'];
                $totalVenda += $subtotal;
                
                $venda->items()->create([
                    'produto_id' => $produto->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'cost_price' => $produto->purchase_price,
                    'subtotal'   => $subtotal,
                ]);
                
                $produto->decrement('stock_quantity', $item['quantity']);
                 // Debugging line to inspect the incoming data
                StockMovement::create([
                    'produto_id' => $produto->id,
                    'type'       => 'out',
                    'quantity'   => $item['quantity'],
                    'reason'     => "Venda: {$venda->invoice_number}",
                    'user_id'    => auth()->id() ?? 1,
                ]);
                }
           

            // 3. Atualizar total da venda
            $venda->update(['total_amount' => $totalVenda]);

            // 4. Lógica Financeira (Pagamentos e Crédito)
            $amountPaid = (float) ($data['amount_paid'] ?? 0);

            if ($venda->status === 'unpaid') {
                // O que sobra para a dívida é: Total - Entrada
                $valorParaDivida = $totalVenda - $amountPaid;

                if (($cliente->current_balance + $valorParaDivida) > $cliente->credit_limit) {
                    throw new \Exception("Venda Bloqueada: Limite de Crédito Excedido.");
                }

                // Se houve entrada, registra o pagamento parcial
                if ($amountPaid > 0) {
                    Pagamento::create([
                        'venda_id'       => $venda->id,
                        'amount_paid'    => $amountPaid,
                        'payment_date'   => now(),
                        'payment_method' => $data['payment_method'] ?? 'Numerário',
                        'reference'      => 'Entrada de venda a prazo',
                    ]);
                }

                // Incrementa apenas o saldo que não foi pago
                $cliente->increment('current_balance', $valorParaDivida);

            } else {
                // Venda "Paid" (Pronto Pagamento) considera o total como pago
                Pagamento::create([
                    'venda_id'       => $venda->id,
                    'amount_paid'    => $totalVenda,
                    'payment_date'   => now(),
                    'payment_method' => $data['payment_method'] ?? 'Numerário',
                    'reference'      => 'Pagamento integral no ato',
                ]);
            }

            return $venda;
        });
    }

    private function generateInvoiceNumber()
    {
        $last = Venda::latest()->first();
        $nextId = $last ? $last->id + 1 : 1;
        return "FT-" . date('Y') . "/" . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }
}