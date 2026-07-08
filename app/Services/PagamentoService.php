<?php

namespace App\Services;

use App\Models\Venda;
use App\Models\Pagamento;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Exception;

class PagamentoService
{
    /**
     * Registra um pagamento (total ou parcial) para uma fatura.
     */
    public function registerPayment(array $data)
    {
        return DB::transaction(function () use ($data) {
            
            $invoice = Venda::findOrFail($data['venda_id']);
            $customer = $invoice->cliente;
            $amountPaid = $data['amount_paid'];
            
            // 1. Validação: O pagamento não pode ser superior ao que falta pagar
            $alreadyPaid = $invoice->pagamentos()->sum('amount_paid');
            $remainingBalance = $invoice->total_amount - $alreadyPaid;
           

            if ($amountPaid > $remainingBalance) {
                throw new Exception("O valor (MZN " . number_format($amountPaid, 2) . ") excede o saldo devedor da fatura (" . number_format($remainingBalance, 2) . ").");
            }

            // 2. Criar o registro do pagamento
            $payment = Pagamento::create([
                'venda_id'     => $invoice->id,
                'amount_paid'    => $amountPaid,
                'payment_date'   => $data['payment_date'] ?? now(),
                'payment_method' => $data['payment_method'], // 'caixa', 'tpa', 'transferencia'
                'reference'      => $data['reference'] ?? null,
            ]);

            // 3. Atualizar o Saldo Devedor do Cliente (Dívida Global)
            // Subtraímos o que ele pagou da conta corrente dele
            $customer->decrement('current_balance', $amountPaid);
            

            // 4. Atualizar o Status da Fatura
            $this->updateInvoiceStatus($invoice);
             

            return $payment;
        });
    }

    /**
     * Define se a fatura está Paga ou Parcialmente Paga
     */
    private function updateInvoiceStatus(Venda $invoice)
    {
       
        $totalPaid = $invoice->pagamentos()->sum('amount_paid');

        if ($totalPaid >= $invoice->total_amount) {
            $invoice->status = 'paid';
        } elseif ($totalPaid > 0) {
            $invoice->status = 'partial';
        } else {
            $invoice->status = 'unpaid';
        }

        $invoice->save();
    }
    public function voidPayment($paymentId)
{
    return DB::transaction(function () use ($paymentId) {
        // 1. Localizar o pagamento
        $payment = Pagamento::findOrFail($paymentId);
        
        // Carregar a fatura e o cliente relacionados
        $invoice = $payment->venda; 
        $customer = $invoice->cliente;
        $amountToRevert = $payment->amount_paid;
        // Se o pagamento é anulado, a dívida do cliente aumenta novamente
        $customer->increment('current_balance', $amountToRevert);

        // 3. Remover o registro do pagamento
        // Nota: Se quiser manter histórico, adicione uma coluna 'status' ou use SoftDeletes
        $payment->delete();

        // 4. Atualizar o Status da Fatura
        // Chamamos a função existente para recalcular se a fatura volta a ser 'partial' ou 'unpaid'
        $this->updateInvoiceStatus($invoice);

        return true;
    });
}
}