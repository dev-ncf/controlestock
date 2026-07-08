@extends('layouts.app')

@section('title', 'Novo Produto')

@section('content')
<div class="max-w-4xl mx-auto animate-in fade-in duration-500">
    
    <header class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-black text-gray-800 tracking-tight uppercase italic">Cadastrar Produto</h1>
            <p class="text-sm text-gray-500 font-medium">Configure os dados técnicos e financeiros do item.</p>
        </div>
        <a href="{{ route('products.index') }}" class="text-gray-400 hover:text-gray-800 transition-colors">
            <i data-lucide="x-circle" class="w-8 h-8"></i>
        </a>
    </header>

    <form action="{{ route('products.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <!-- COLUNA 1: DADOS GERAIS -->
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm space-y-6">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase block mb-2 tracking-widest">Nome do Produto</label>
                        <input type="text" name="name" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-100" required placeholder="Ex: Óleo San Drop">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase block mb-2 tracking-widest">Categoria</label>
                            <select name="categoria_id" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-100" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase block mb-2 tracking-widest">Stock Inicial</label>
                            <input type="number" name="stock_quantity" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-100" required>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase block mb-2 tracking-widest">Stock Mínimo</label>
                            <input type="number" name="min_stock" value="5" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-100">
                        </div>
                    </div>
                </div>

                <!-- SEÇÃO: RELAÇÃO CAIXA / AVULSO (NOVO) -->
                <div class="bg-blue-50 p-8 rounded-[2.5rem] border border-blue-100 space-y-6">
                    <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="layers" class="text-blue-600 w-5 h-5"></i>
                        <h3 class="text-xs font-black text-blue-600 uppercase tracking-widest">Configuração de Venda Fracionada</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] font-black text-blue-400 uppercase block mb-2 tracking-widest">Este produto é um Avulso de:</label>
                            <select name="produto_pai_id" class="w-full bg-white border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-200">
                                <option value="">Nenhum (Este produto é uma Caixa ou Normal)</option>
                                @foreach($all_products as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} (Caixa)</option>
                                @endforeach
                            </select>
                            <p class="text-[9px] text-blue-400 mt-2 italic">Selecione a Caixa caso este produto seja vendido por unidade.</p>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-blue-400 uppercase block mb-2 tracking-widest">Unidades por Caixa</label>
                            <input type="number" name="fator_conversao" value="1" class="w-full bg-white border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-200">
                            <p class="text-[9px] text-blue-400 mt-2 italic">Ex: Se 1 caixa tem 4 galões, coloque 4.</p>
                        </div>
                    </div>
                </div>
            </div>

           <!-- COLUNA 2: FINANCEIRO (LADO DIREITO) -->
            <div class="space-y-6">
                <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-xl text-white space-y-6">
                    <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest border-b border-slate-800 pb-4 italic">Financeiro</h3>

                    <!-- PREÇO DE CUSTO -->
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase block mb-2 tracking-widest">Preço de Compra (Custo)</label>
                        <input type="number" step="0.01" name="purchase_price" 
                            class="w-full bg-slate-800 border-none rounded-2xl p-4 text-sm font-black text-blue-400 focus:ring-2 focus:ring-blue-500 shadow-inner" 
                            required placeholder="0.00">
                    </div>

                    <!-- PREÇO DE VENDA (Informado Manualmente) -->
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase block mb-2 tracking-widest">Preço de Venda</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-500 uppercase font-mono">MZN</span>
                            <input type="number" step="0.01" name="sale_price" 
                                class="w-full bg-slate-800 border-none rounded-2xl p-4 pl-14 text-2xl font-black text-green-400 focus:ring-2 focus:ring-green-500 shadow-inner" 
                                required placeholder="0.00">
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-5 rounded-3xl font-black text-xs uppercase tracking-widest transition-all shadow-lg shadow-blue-900/20 active:scale-95">
                            Gravar Produto
                        </button>
                        <a href="{{ route('products.index') }}" class="block text-center mt-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest hover:text-white transition-colors">Cancelar</a>
                    </div>
                </div>
                
                <div class="p-6 bg-white rounded-3xl border border-gray-100 shadow-sm italic text-gray-400 text-xs">
                    <i data-lucide="info" class="w-4 h-4 mb-2"></i>
                    Certifique-se de que o preço de venda é superior ao preço de custo para garantir a rentabilidade.
                </div>
            </div>
        </div>
    </form>
</div>

@push('js')
<script>
    // Selecionar os inputs
    const custoInput = document.querySelector('input[name="purchase_price"]');
    const markupInput = document.querySelector('input[name="markup"]');
    const vendaDisplay = document.querySelector('#venda_display');

    // Função de cálculo
    function calcular() {
        let custo = parseFloat(custoInput.value || 0);
        let markup = parseFloat(markupInput.value || 0);
        
        // Preço de Venda = Custo + (Custo * Markup / 100)
        let valorVenda = custo * (1 + (markup / 100));
        
        // Formatar para moeda Moçambicana (MZN)
        vendaDisplay.innerText = valorVenda.toLocaleString('pt-MZ', { 
            minimumFractionDigits: 2, 
            maximumFractionDigits: 2 
        });
    }

    // Ouvir as mudanças nos campos
    custoInput.addEventListener('input', calcular);
    markupInput.addEventListener('input', calcular);

    // Executar uma vez ao carregar caso existam valores preenchidos
    window.addEventListener('DOMContentLoaded', calcular);
</script>
@endpush
@endsection