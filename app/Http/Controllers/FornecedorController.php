<?php

namespace App\Http\Controllers;

use App\Models\Fornecedor;
use Illuminate\Http\Request;

class FornecedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Fornecedor::orderBy('name')->get();
        $totalToPay = Fornecedor::sum('balance_to_pay'); // Soma de todas as nossas dívidas

        return view('admin.suppliers.index', compact('suppliers', 'totalToPay'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nif' => 'nullable|string|unique:fornecedores,nif',
            'phone' => 'nullable|string',
        ]);

        Fornecedor::create($validated);
        return redirect()->back()->with('success', 'Fornecedor registado!');
    }

    public function show($id)
    {
        $supplier = Fornecedor::findOrFail($id);
        // Pega as entradas de stock feitas com este fornecedor
        $history = \App\Models\StockMovement::where('reason', 'like', "%{$supplier->name}%")
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('admin.suppliers.show', compact('supplier', 'history'));
    }

    // Método para registar que pagamos ao fornecedor
    public function liquidar(Request $request)
{
    // 1. Primeiro buscamos o fornecedor para saber o saldo atual
    $supplier = Fornecedor::findOrFail($request->fornecedor_id);

    // 2. Validamos, garantindo que o valor não exceda a dívida (convertida para positivo com abs)
    $request->validate([
        'fornecedor_id' => 'required|exists:fornecedores,id',
        'amount' => [
            'required',
            'numeric',
            'min:1',
            'max:' . abs($supplier->balance_to_pay) // Não permite pagar mais do que deve
        ]
    ]);

    // 3. Como a dívida é negativa (ex: -50.000), ao "Somar" (increment),
    // ela se aproxima de zero (ex: -50.000 + 10.000 = -40.000)
    $supplier->decrement('balance_to_pay', $request->amount);

    return redirect()->back()->with('success', 'Pagamento ao fornecedor registado com sucesso!');
}
    public function addDivida(Request $request)
    {
        $request->validate([
            'fornecedor_id' => 'required|exists:fornecedores,id',
            'amount' => 'required|numeric|min:1'
        ]);

        $supplier = Fornecedor::findOrFail($request->fornecedor_id);

        // Deduzimos o valor da nossa dívida
        $supplier->increment('balance_to_pay', $request->amount);

        return redirect()->back()->with('success', 'Pagamento ao fornecedor registado com sucesso!');
    }

   public function destroy($id)
{
    $supplier = Fornecedor::findOrFail($id);

    // Se o saldo for diferente de 0 (seja positivo ou negativo), impede a exclusão
    // Usamos o round para evitar problemas com centavos infinitesimais do float
    if (round($supplier->balance_to_pay, 2) != 0) {
        return redirect()->back()->with('error', 'Não pode apagar um fornecedor com saldo pendente (MZN ' . number_format($supplier->balance_to_pay, 2) . ')!');
    }

    $supplier->delete();

    // REDIRECIONAR PARA A LISTA, NÃO PARA TRÁS
    return redirect()->route('suppliers.index')->with('success', 'Fornecedor removido com sucesso.');
}
}
