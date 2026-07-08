<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Models\Produto;
use App\Models\Cliente;
use App\Services\VendaService;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;

class VendaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index()
    {
        $invoices = Venda::with('cliente')->orderBy('date', 'desc')->paginate(10);
        return view('admin.invoices.index', compact('invoices'));
    }

//    public function create(Request $request) {
//     $search = $request->query('search');

//     $products = Produto::where('stock_quantity', '>', 0)
//         ->when($search, function($q) use ($search) {
//             $q->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%");
//         })
//         ->limit(9)->get();

//     $customers = Cliente::all();
//     $cart = session()->get('cart', []);
//     $total = collect($cart)->sum('subtotal');

//     return view('admin.invoices.create', compact('products', 'customers', 'cart', 'total'));
// }

  public function addItem(Request $request)
{
    // Carregamos o produto com a relação 'pai' (a caixa)
    $product = Produto::with('pai')->findOrFail($request->product_id);
    $qty = $request->quantity ?? 1;

    // --- LÓGICA DE VALIDAÇÃO DE STOCK VIRTUAL ---

    // 1. Começamos com o stock que está "solto" (avulso)
    $stockTotalDisponivel = $product->stock_quantity;

    // 2. Se o produto tiver um "Pai" (Caixa), somamos o que pode ser desmembrado
    if ($product->produto_pai_id && $product->pai) {
        $unidadesNasCaixas = $product->pai->stock_quantity * $product->fator_conversao;
        $stockTotalDisponivel += $unidadesNasCaixas;
    }

    // 3. Verificamos se a quantidade desejada é maior que o stock TOTAL (Virtual)
    if($stockTotalDisponivel < $qty) {
        return redirect()->back()->with('error', "Stock insuficiente! O máximo disponível (incluindo caixas) é: {$stockTotalDisponivel}");
    }

    // --- FIM DA VALIDAÇÃO ---

    // 1. Pegar todos os carrinhos e qual está ativo
    $multiCarts = session()->get('multi_carts', ['default' => ['label' => 'Balcão', 'items' => []]]);
    $activeCartId = session()->get('active_cart', 'default');

    // 2. Adicionar o item ao carrinho que está aberto no momento
    $multiCarts[$activeCartId]['items'][] = [
        'produto_id' => $product->id,
        'name' => $product->name,
        'quantity' => $qty,
        'unit_price' => $product->sale_price,
        'subtotal' => $product->sale_price * $qty
    ];

    // 3. Guardar de volta na sessão
    session()->put('multi_carts', $multiCarts);

    return redirect()->back();
}
    public function removeItem($index)
{
    $multiCarts = session()->get('multi_carts', []);
    $activeCartId = session()->get('active_cart', 'default');

    if (isset($multiCarts[$activeCartId]['items'][$index])) {
        unset($multiCarts[$activeCartId]['items'][$index]);
        // Reorganiza os índices para não dar erro no loop
        $multiCarts[$activeCartId]['items'] = array_values($multiCarts[$activeCartId]['items']);
    }

    session()->put('multi_carts', $multiCarts);
    return redirect()->back();
}

  public function store(Request $request, VendaService $service)
{
    $multiCarts = session()->get('multi_carts', []);
    $activeCartId = session()->get('active_cart', 'default');

    $cartItems = $multiCarts[$activeCartId]['items'] ?? [];

    if (empty($cartItems)) {
        return redirect()->back()->with('error', 'Esta conta está vazia!');
    }

    // Validação básica
    $request->validate([
        'customer_id' => 'required|exists:clientes,id',
        'status' => 'required|in:paid,unpaid',
        'amount_paid' => 'nullable|numeric|min:0',
        'due_date' => 'nullable|date',
    ]);

    try {
        $invoice = $service->createInvoice([
            'cliente_id'    => $request->customer_id,
            'status'        => $request->status,
            'due_date'      => $request->due_date,
            'amount_paid'   => $request->amount_paid ?? 0, // Valor de entrada
            'items'         => $cartItems,
            'payment_method' => $request->payment_method ?? 'Numerário'
        ]);

        // Limpeza do carrinho processado
        unset($multiCarts[$activeCartId]);

        if (empty($multiCarts)) {
            $multiCarts = ['default' => ['label' => 'Balcão', 'items' => []]];
            session()->put('active_cart', 'default');
        } else {
            session()->put('active_cart', array_key_first($multiCarts));
        }

        session()->put('multi_carts', $multiCarts);

        return redirect()->route('invoices.show', $invoice->id)->with('success', 'Venda finalizada!');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', $e->getMessage());
    }
}

    /**
     * Display the specified resource.
     */
    public function show($id)
    {

    $invoice = Venda::with(['cliente', 'items', 'pagamentos'])->findOrFail($id);


    $totalPaid = $invoice->pagamentos->sum('amount_paid');
    $remaining = $invoice->total_amount - $totalPaid;

    return view('admin.invoices.show', compact('invoice', 'totalPaid', 'remaining'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Venda $venda)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venda $venda)
    {
        //
    }

 public function imprimir($id)
    {
        //
        $venda = Venda::with(['cliente', 'items', 'pagamentos'])->findOrFail($id);

    // Calcula o que falta pagar para mostrar no PDF
        $totalPago = $venda->pagamentos->sum('amount_paid');
        $falta = $venda->total_amount - $totalPago;

        $pdf = Pdf::loadView('admin.pdf.factura', compact('venda', 'totalPago', 'falta'));

    // Retorna o PDF para abrir no navegador
     $nomeArquivo = str_replace(['/', '\\'], '-', $venda->invoice_number);
    return $pdf->stream("Fatura_{$nomeArquivo}.pdf");
    }
    public function talao($id)
        {
            //
            $venda = Venda::with(['cliente', 'items', 'pagamentos'])->findOrFail($id);

        // Calcula o que falta pagar para mostrar no PDF
            $totalPago = $venda->pagamentos->sum('amount_paid');
            $falta = $venda->total_amount - $totalPago;

            $pdf = Pdf::loadView('admin.pdf.talao', compact('venda', 'totalPago', 'falta'))
            ->setPaper([0, 0, 226.77, 800], 'portrait')
            ->setOption(['isRemoteEnabled' => true]);

        // Retorna o PDF para abrir no navegador
        $nomeArquivo = str_replace(['/', '\\'], '-', $venda->invoice_number);
        return $pdf->stream("Talao__{$nomeArquivo}.pdf");
        }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venda $venda)
    {
        //
    }
    public function create(Request $request) {
    // $search = $request->query('search');
    $products = Produto::with('pai')->where('stock_quantity', '>', 0)->orWhereHas('pai', function($q){ $q->where('stock_quantity', '>', 0); })->get();
    $customers = Cliente::all();

    // Gestão de Carrinhos
    $multiCarts = session()->get('multi_carts', [
        'default' => ['label' => 'Balcão', 'items' => [], 'total' => 0]
    ]);
    $activeCartId = session()->get('active_cart', 'default');

    $cart = $multiCarts[$activeCartId]['items'];
    $total = collect($cart)->sum('subtotal');

    return view('admin.invoices.create', compact('products', 'customers', 'cart', 'total', 'multiCarts', 'activeCartId'));
}

public function novoCarrinho(Request $request) {
    $id = uniqid();
    $label = $request->label ?? 'Mesa ' . (session()->get('multi_carts') ? count(session()->get('multi_carts')) + 1 : 1);

    $multiCarts = session()->get('multi_carts', []);
    $multiCarts[$id] = ['label' => $label, 'items' => [], 'total' => 0];

    session()->put('multi_carts', $multiCarts);
    session()->put('active_cart', $id);

    return redirect()->back();
}

public function trocarCarrinho($id) {
    session()->put('active_cart', $id);
    return redirect()->back();
}
public function removerMesa($id)
{
    $multiCarts = session()->get('multi_carts', []);

    if (isset($multiCarts[$id])) {
        unset($multiCarts[$id]);
    }

    // Se não sobrarem mesas, volta para a padrão
    if (empty($multiCarts)) {
        $multiCarts['default'] = ['label' => 'Balcão', 'items' => []];
        session()->put('active_cart', 'default');
    } else {
        session()->put('active_cart', array_key_first($multiCarts));
    }

    session()->put('multi_carts', $multiCarts);
    return redirect()->back();
}
public function updateItem(Request $request, $index)
{
    // 1. Pegar a estrutura completa dos carrinhos e qual está ativo
    $multiCarts = session()->get('multi_carts', ['default' => ['label' => 'Balcão', 'items' => []]]);
    $activeCartId = session()->get('active_cart', 'default');

    // 2. Verificar se o item existe no carrinho ativo
    if (isset($multiCarts[$activeCartId]['items'][$index])) {

        $newPrice = (float) $request->unit_price;
        $newQty = (int) $request->quantity;

        // 3. Atualizar os valores (Preço editável e Quantidade)
        $multiCarts[$activeCartId]['items'][$index]['unit_price'] = $newPrice;
        $multiCarts[$activeCartId]['items'][$index]['quantity'] = $newQty;

        // Recalcular o subtotal deste item
        $multiCarts[$activeCartId]['items'][$index]['subtotal'] = $newPrice * $newQty;

        // 4. Salvar de volta na sessão
        session()->put('multi_carts', $multiCarts);

        return redirect()->back();
    }

    return redirect()->back()->with('error', 'Item não encontrado.');
}
}
