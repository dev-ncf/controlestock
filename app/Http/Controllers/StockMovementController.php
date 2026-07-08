<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Produto;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $query = StockMovement::with(['produto', 'user']);

        // Filtro por Produto
        if ($request->filled('produto_id')) {
            $query->where('produto_id', $request->produto_id);
        }

        // Filtro por Tipo (In/Out)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtro por Data
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $movements = $query->orderBy('created_at', 'desc')->paginate(20);
        $products = Produto::orderBy('name')->get();

        return view('admin.stock.movements', compact('movements', 'products'));
    
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(StockMovement $stockMovement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockMovement $stockMovement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockMovement $stockMovement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockMovement $stockMovement)
    {
        //
    }
    public function ajuste(Request $request)
{
    $request->validate([
        'produto_id' => 'required|exists:produtos,id',
        'type' => 'required|in:in,out',
        'quantity' => 'required|integer|min:1',
        'reason' => 'required|string|max:255',
    ]);

    \DB::transaction(function () use ($request) {
        $produto = Produto::findOrFail($request->produto_id);

        if ($request->type === 'out') {
            $produto->decrement('stock_quantity', $request->quantity);
        } else {
            $produto->increment('stock_quantity', $request->quantity);
        }

        StockMovement::create([
            'produto_id' => $produto->id,
            'type' => $request->type,
            'quantity' => $request->quantity,
            'reason' => 'AJUSTE: ' . $request->reason,
            'user_id' => auth()->id() ?? 1,
        ]);
    });

    return redirect()->back()->with('success', 'Ajuste de stock realizado com sucesso!');
}
}
