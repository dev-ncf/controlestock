<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Categoria;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     // Listar produtos com pesquisa
    public function index(Request $request)
    {
        $search = $request->query('search');

        $products = Produto::with('categoria')
            ->when($search, function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10);
    $all_products = Produto::with('categoria')->get();
        return view('admin.produtos.index', compact('products', 'all_products', 'search'));
    }

    // Mostrar formulário de criação
    public function create()
    {
        $categories = Categoria::all();
         $all_products = Produto::orderBy('name')->get(); // Para escolher o "Pai"
        return view('admin.produtos.create', compact('categories', 'all_products'));
    }

    // Salvar o produto no Banco de Dados
   public function store(Request $request)
{
    // 1. Validação (Incluímos os novos campos e tornamos markup/venda opcionais)
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'categoria_id' => 'required|exists:categorias,id',
        'purchase_price' => 'required|numeric|min:0',
        'markup' => 'nullable|numeric', 
        'sale_price' => 'nullable|numeric',
        'stock_quantity' => 'required|integer|min:0',
        'min_stock' => 'required|integer|min:0',
        'produto_pai_id' => 'nullable|exists:produtos,id',
        'fator_conversao' => 'nullable|integer|min:1',
    ]);

    $custo = $validated['purchase_price'];
    $markup = $request->markup;
    $venda = $request->sale_price;
    $validated['sku'] = $request->sku ?? strtoupper(substr($validated['name'], 0, 3)) . '-' . rand(1000, 9999);
    // 2. LÓGICA DE CÁLCULO REVERSO
    if ($venda && $venda > 0) {
        // Se o utilizador forneceu o PREÇO DE VENDA, calculamos o markup
        // Fórmula: ((Venda - Custo) / Custo) * 100
        $validated['sale_price'] = $venda;
        $validated['markup'] = $custo > 0 ? (($venda - $custo) / $custo) * 100 : 0;
    } 
    elseif ($markup !== null) {
        // Se o utilizador forneceu apenas o MARKUP, calculamos o preço de venda
        // Fórmula: Custo * (1 + Markup / 100)
        $validated['markup'] = $markup;
        $validated['sale_price'] = $custo * (1 + ($markup / 100));
    }

    // 3. Criar o Produto com os dados processados
    $produto = Produto::create($validated);

    // 4. Registo de Movimentação Inicial
    if ($produto->stock_quantity > 0) {
        \App\Models\StockMovement::create([
            'produto_id' => $produto->id,
            'type' => 'in',
            'quantity' => $produto->stock_quantity,
            'reason' => 'Stock inicial no cadastro',
            'user_id' => auth()->id() ?? 1,
        ]);
    }

    return redirect()->route('products.index')->with('success', 
        "Produto '{$produto->name}' cadastrado. Preço: MZN " . number_format($produto->sale_price, 2) . 
        " (Lucro: " . number_format($produto->markup, 1) . "%)"
    );
}

    /**
     * Display the specified resource.
     */
    public function show(Produto $produto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
   public function edit($id)
{
    $product = Produto::findOrFail($id);
    $categories = Categoria::all();
    $all_products = Produto::all();
    return view('admin.produtos.edit', compact('product', 'categories', 'all_products'));
}

public function update(Request $request, $id)
{
    $product = Produto::findOrFail($id);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'sku' => 'required|string|unique:produtos,sku,' . $id,
        'categoria_id' => 'required|exists:categorias,id',
        'purchase_price' => 'required|numeric|min:0',
        'sale_price' => 'required|numeric|min:0', // Agora validamos o preço vindo do formulário
        'min_stock' => 'required|integer|min:0',
        'produto_pai_id' => 'nullable|exists:produtos,id', // Caso use a relação de caixa
        'fator_conversao' => 'nullable|numeric|min:1',     // Caso use a relação de caixa
    ]);

    // O $product->update($validated) já vai salvar o sale_price 
    // porque ele está presente no array de dados validados.
    $product->update($validated);

    return redirect()->route('products.index')
        ->with('success', 'Produto "' . $product->name . '" atualizado com sucesso!');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produto $produto)
    {
        //
    }
    public function shelf()
{
    // Filtramos produtos que têm um Pai (ou seja, são avulsos)
    $products = Produto::whereNotNull('produto_pai_id')
                ->with('pai') // Carrega a caixa para sabermos o stock reserva
                ->orderBy('name')
                ->get();

    return view('admin.produtos.shelf', compact('products'));
}

public function openBox($id)
{
    $produtoFilho = Produto::findOrFail($id);
    $produtoPai = $produtoFilho->pai;

    // 1. Verificações de segurança
    if (!$produtoPai) {
        return redirect()->back()->withErrors(['error' => 'Este produto não está vinculado a nenhuma caixa.']);
    }

    if ($produtoPai->stock_quantity <= 0) {
        return redirect()->back()->withErrors(['error' => 'Não há caixas de "' . $produtoPai->name . '" em stock para abrir.']);
    }

    // 2. Operação de Troca (Transação para garantir integridade)
    \DB::transaction(function () use ($produtoFilho, $produtoPai) {
        // Tira 1 da caixa
        $produtoPai->decrement('stock_quantity', 1);

        // Adiciona as unidades ao avulso
        $produtoFilho->increment('stock_quantity', $produtoFilho->fator_conversao);

        // Regista o movimento no histórico para auditoria
        StockMovement::create([
            'produto_id' => $produtoPai->id,
            'type'       => 'out',
            'quantity'   => 1,
            'reason'     => "ABERTURA MANUAL: Abastecimento de prateleira ({$produtoFilho->name})",
            'user_id'    => auth()->id() ?? 1,
        ]);
        
        StockMovement::create([
            'produto_id' => $produtoFilho->id,
            'type'       => 'in',
            'quantity'   => $produtoFilho->fator_conversao,
            'reason'     => "ABERTURA MANUAL: Entrada vinda da caixa ({$produtoPai->name})",
            'user_id'    => auth()->id() ?? 1,
        ]);
    });

    return redirect()->back()->with('success', 'Caixa aberta! Prateleira abastecida com +' . $produtoFilho->fator_conversao . ' unidades.');
}

public function storeShelfProduct(Request $request)
{
    $parent = \App\Models\Produto::findOrFail($request->produto_pai_id);

    $validated = $request->validate([
        'produto_pai_id' => 'required|exists:produtos,id',
        'sale_price' => 'required|numeric|min:0',
        'fator_conversao' => 'required|integer|min:1',
    ]);

    // 1. GERAÇÃO AUTOMÁTICA DE NOME
    $validated['name'] = $parent->name . ' - AVULSO';
    
    // 2. GERAÇÃO AUTOMÁTICA DE SKU
    $newSku = $parent->sku . '-AV';
    
    // Verificação de segurança: se o SKU já existir, adiciona um sufixo único
    if (\App\Models\Produto::where('sku', $newSku)->exists()) {
        $newSku = $newSku . rand(10, 99);
    }
    $validated['sku'] = $newSku;

    // 3. HERANÇA DE DADOS TÉCNICOS
    $validated['categoria_id'] = $parent->categoria_id;
    $validated['purchase_price'] = $parent->purchase_price / $validated['fator_conversao'];
    $validated['markup'] = 0; 
    $validated['stock_quantity'] = 0; 
    $validated['min_stock'] = 5;

    \App\Models\Produto::create($validated);

    return redirect()->back()->with('success', 'Produto avulso criado com SKU: ' . $newSku);
}

public function destroyShelfProduct($id)
{
    $product = \App\Models\Produto::findOrFail($id);
    
    if ($product->stock_quantity > 0) {
        return redirect()->back()->withErrors(['error' => 'Não pode remover um produto que ainda tem unidades na prateleira. Venda-as primeiro ou faça um ajuste de stock.']);
    }

    $product->delete();
    return redirect()->back()->with('success', 'Produto removido da prateleira.');
}
public function addProductShelf(Request $request, $id) {
    $product = Produto::findOrFail($id);
    $product->increment('stock_quantity', $request->quantity);
    return back()->with('success', 'Stock atualizado com sucesso!');
}
}
