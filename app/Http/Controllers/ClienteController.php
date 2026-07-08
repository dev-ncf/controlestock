<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClienteController extends Controller
{
   public function index(Request $request)
    {
        $search = $request->query('search');

        $customers = Cliente::when($search, function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('nif', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->get();

        // Soma total de quanto todos os clientes devem (Dívida Global)
        $totalDebt = Cliente::sum('current_balance');

        return view('admin.clientes.index', compact('customers', 'totalDebt'));
    }

    public function create()
    {
        return view('admin.clientes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nif' => 'nullable|string|unique:clientes,nif',
            'phone' => 'nullable|string',
            'credit_limit' => 'required|numeric|min:0',
        ]);

        Cliente::create($validated);

        return redirect()->route('customers.index')->with('success', 'Cliente cadastrado com sucesso!');
    }

    // Mostrar o perfil do cliente e o histórico de faturas/dívidas
    public function show($id)
    {
        $customer = Cliente::with(['vendas' => function($q) {
            $q->orderBy('date', 'desc');
        }])->findOrFail($id);

        return view('admin.clientes.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
{
    // Verifica se o cliente tem vendas ou pagamentos
    if ($cliente->vendas()->count() > 0) {
        return redirect()->back()->with('error', 'Não é possível eliminar este cliente porque existem vendas associadas a ele. Tente desativá-lo ou manter o registo.');
    }

    try {
        $cliente->delete();
        return redirect()->route('customers.index')->with('success', 'Cliente eliminado com sucesso!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Ocorreu um erro ao tentar eliminar o cliente.');
    }
}

    // No seu Controller de Clientes

public function receivePayment(Request $request)
{
    $customer = Cliente::findOrFail($request->customer_id);

    
    $customer->decrement('current_balance', $request->amount);

    return redirect()->back()->with('success', 'Pagamento recebido!');
}

public function addDebt(Request $request)
{
    $customer = Cliente::findOrFail($request->customer_id);

    // Aumenta o que ele deve
    $customer->increment('current_balance', $request->amount);

    return redirect()->back()->with('success', 'Débito registado!');
}

public function extract($id)
{
    $customer = Cliente::findOrFail($id);
    
    // Pegamos todas as vendas
    $vendas = $customer->vendas()->select('id', 'date', 'invoice_number as ref', 'total_amount as debit', \DB::raw('0 as credit'))->get();
    
    // Pegamos todos os pagamentos realizados
    $pagamentos = \App\Models\Pagamento::whereHas('venda', function($q) use ($id) {
        $q->where('cliente_id', $id);
    })->select('id', 'payment_date as date', 'reference as ref', \DB::raw('0 as debit'), 'amount_paid as credit')->get();

    // Unimos e ordenamos por data
    $ledger = $vendas->concat($pagamentos)->sortBy('date');

    return view('admin.clientes.extract', compact('customer', 'ledger'));
}

public function extractPdf($id)
{
    $customer = Cliente::findOrFail($id);

    // 1. Débitos (Vendas) com Itens e Produtos
    $vendas = $customer->vendas()
        ->with('items.produto') // Carrega os produtos da venda
        ->get()
        ->map(function($venda) {
            return (object)[
                'date' => $venda->created_at,
                'ref' => $venda->invoice_number,
                'valor' => $venda->total_amount,
                'tipo' => 'venda',
                'items' => $venda->items // Passamos a coleção de itens para a view
            ];
        });

    // 2. Créditos (Pagamentos)
    $pagamentos = \App\Models\Pagamento::whereHas('venda', function($q) use ($id) {
        $q->where('cliente_id', $id);
    })->select('created_at as date', 'reference as ref', 'amount_paid as valor', \DB::raw("'pagamento' as tipo"))
      ->get()
      ->map(function($p) {
          $p->items = null; // Pagamentos não têm itens de produto
          return $p;
      });

    // 3. Unir e Ordenar
    $ledger = $vendas->concat($pagamentos)->sortBy('date');

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.pdf.extract', compact('customer', 'ledger'))
        ->setPaper('a4', 'portrait');

    return $pdf->stream("Extrato_{$customer->name}.pdf");
}
}
