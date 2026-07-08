<?php

namespace App\Http\Controllers;

use App\Models\Pagamento;
use App\Models\Venda;
use App\Services\PagamentoService;
use Illuminate\Http\Request;

class PagamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Lista apenas faturas com dívida
    public function index(Request $request)
    {
        $search = $request->query('search');

        $debts = Venda::with('cliente')
            ->whereIn('status', ['unpaid', 'partial'])
            ->when($search, function($query) use ($search) {
                $query->whereHas('cliente', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhere('invoice_number', 'like', "%{$search}%");
            })
            ->orderBy('due_date', 'asc')
            ->paginate(10);

        return view('admin.receipts.index', compact('debts'));
    }

    // Mostra o formulário de pagamento para uma fatura
    public function create($invoice_id)
    {
        $invoice = Venda::with('cliente', 'pagamentos')->findOrFail($invoice_id);

        // Calcula quanto já foi pago e quanto falta
        $alreadyPaid = $invoice->pagamentos->sum('amount_paid');
        $remaining = $invoice->total_amount - $alreadyPaid;

        return view('admin.receipts.create', compact('invoice', 'remaining'));
    }

    // Grava o pagamento usando o Service que criamos anteriormente
    public function store(Request $request, PagamentoService $service)
{
    $request->validate([
        'venda_id' => 'required|exists:vendas,id',
        'amount_paid' => 'required|numeric|min:1',
        'payment_method' => 'required',
    ]);

    try {
        // 1. O service retorna o objeto Pagamento criado
        $payment = $service->registerPayment($request->all());

        // 2. Acedemos à venda e depois ao cliente_id
        // Certifique-se que o model Pagamento tem a relação 'venda' definida
        $venda = $payment->venda;
        $clienteId = $venda->cliente_id;
        // dd($clienteId); // Debug: Verifica se a relação 'venda' está carregada corretamente

        return redirect()
            ->route('customers.show', $clienteId)
            ->with('success', 'Pagamento registado com sucesso!');
            
    } catch (\Exception $e) {
        return redirect()->back()->withErrors('error', $e->getMessage());
    }
}

    /**
     * Display the specified resource.
     */
    public function show(Pagamento $pagamento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pagamento $pagamento)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pagamento $pagamento)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, PagamentoService $service)
    {
        try {
            $payment = Pagamento::findOrFail($id);
            $service->voidPayment($id);
             $venda = $payment->venda;
             $clienteId = $venda->cliente_id;
            return redirect()
            ->route('customers.show', $clienteId)
                                ->with('success', 'Pagamento removido com sucesso. O saldo foi atualizado.');
        } catch (\Exception $e) {
            return redirect()->back()
                                ->withErrors(['error' => $e->getMessage()]);
        }
    }
      
}
