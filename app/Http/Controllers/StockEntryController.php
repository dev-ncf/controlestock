<?php
namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Fornecedor;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockEntryController extends Controller
{
    public function index()
    {
        // Histórico de movimentos de entrada
        $movements = StockMovement::with('produto')
            ->where('type', 'in')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.stock.entries.index', compact('movements'));
    }

    public function create()
    {
        $suppliers = Fornecedor::all();
        $products = Produto::orderBy('name')->get();
        $cart = session()->get('stock_cart', []);
        $total = collect($cart)->sum('subtotal');

        return view('admin.stock.entries.create', compact('suppliers', 'products', 'cart', 'total'));
    }

    public function addItem(Request $request)
    {
        $product = Produto::findOrFail($request->product_id);
        
        $cart = session()->get('stock_cart', []);
        $cart[] = [
            'produto_id' => $product->id,
            'name' => $product->name,
            'quantity' => $request->quantity,
            'purchase_price' => $request->purchase_price, // Preço que o fornecedor cobrou
            'subtotal' => $request->quantity * $request->purchase_price
        ];

        session()->put('stock_cart', $cart);
        return redirect()->back();
    }

    public function removeItem($index)
    {
        $cart = session()->get('stock_cart', []);
        unset($cart[$index]);
        session()->put('stock_cart', array_values($cart));
        return redirect()->back();
    }

    public function store(Request $request, StockService $service)
    {
        $cart = session()->get('stock_cart', []);
        
        if (empty($cart)) return redirect()->back()->with('error', 'Carrinho vazio!');
        
        try {
            // dd($request->supplier_id);
            foreach ($cart as $item) {
                $service->registerStockEntry([
                    'produto_id' => $item['produto_id'],
                    'fornecedor_id' => $request->supplier_id,
                    'quantity' => $item['quantity'],
                    'purchase_price' => $item['purchase_price'],
                    'payment_status' => $request->payment_status // 'paid' ou 'unpaid'
                    ]);
                    }

            session()->forget('stock_cart');
            return redirect()->route('stock.entries.index')->with('success', 'Stock atualizado!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}