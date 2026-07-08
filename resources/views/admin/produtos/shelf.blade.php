@extends('layouts.app')

@section('content')
<div class="space-y-6 animate-in fade-in duration-500" 
     x-data="{ 
        search: '', 
        openAdd: false,
        openAdjust: false,
        selectedProduct: { id: null, name: '' },
        allProducts: {{ $products->toJson() }},
        
        get filteredProducts() {
            if (this.search.trim() === '') return this.allProducts;
            return this.allProducts.filter(p => 
                p.name.toLowerCase().includes(this.search.toLowerCase()) || 
                (p.sku && p.sku.toLowerCase().includes(this.search.toLowerCase()))
            );
        },

        prepAdjust(product) {
            this.selectedProduct = product;
            this.openAdjust = true;
        }
     }">
    
    <!-- Cabeçalho e Filtro -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
        <div>
            <h1 class="text-xl font-black text-gray-800 uppercase italic">Prateleira</h1>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Controle de Unidades</p>
        </div>

        <div class="flex-1 max-w-md relative">
            <i data-lucide="search" class="w-4 h-4 absolute left-3 top-3 text-gray-300"></i>
            <input type="text" x-model="search" 
                placeholder="Filtrar por nome ou SKU..." 
                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-blue-100 transition-all">
        </div>

        <button @click="openAdd = true" class="bg-blue-600 text-white px-5 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all flex items-center gap-2">
            <i data-lucide="plus-circle" class="w-4 h-4"></i> Novo Vínculo
        </button>
    </div>

    <!-- Grid 4 Colunas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <template x-for="p in filteredProducts" :key="p.id">
            <div class="bg-white rounded-[1.5rem] border border-gray-100 shadow-sm hover:shadow-md transition-all flex flex-col group relative overflow-hidden">
                
                <!-- Botão Remover -->
                <form :action="'{{ route('products.destroyShelf', ':id') }}'.replace(':id', p.id)" method="POST" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                    @csrf @method('DELETE')
                    <button type="submit" onclick="return confirm('Remover da prateleira?')" class="p-1.5 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    </button>
                </form>

                <div class="p-5">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-[8px] font-black bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded uppercase font-mono" x-text="p.sku || 'S/ SKU'"></span>
                        <template x-if="p.stock_quantity <= p.min_stock">
                            <span class="w-2 h-2 bg-red-500 rounded-full animate-ping"></span>
                        </template>
                    </div>
                    
                    <h3 class="font-black text-gray-800 text-[11px] uppercase leading-tight truncate mb-1" x-text="p.name"></h3>
                    <p class="text-[8px] text-gray-400 font-bold uppercase truncate italic">
                        Origem: <span x-text="p.pai ? p.pai.name : '---'"></span>
                    </p>
                </div>

                <!-- Painel de Stocks -->
                <div class="grid grid-cols-2 border-y border-gray-50 bg-gray-50/50">
                    <div class="p-3 text-center border-r border-gray-50 relative group/stock">
                        <p class="text-[7px] font-bold text-gray-400 uppercase">Na Prateleira</p>
                        <p class="text-sm font-black" :class="p.stock_quantity <= 0 ? 'text-red-600' : 'text-gray-700'" x-text="p.stock_quantity + ' un'"></p>
                        
                        <!-- Botão Ajuste Manual (Aparece no Hover do setor de stock) -->
                        <button @click="prepAdjust(p)" class="absolute inset-0 bg-blue-600/90 text-white opacity-0 group-hover/stock:opacity-100 transition-opacity flex items-center justify-center gap-1 text-[8px] font-black uppercase">
                            <i data-lucide="plus" class="w-3 h-3"></i> Ajustar
                        </button>
                    </div>
                    <div class="p-3 text-center">
                        <p class="text-[7px] font-bold text-gray-400 uppercase">No Armazém</p>
                        <p class="text-sm font-black text-blue-600" x-text="(p.pai ? p.pai.stock_quantity : 0) + ' cx'"></p>
                    </div>
                </div>

                <!-- Ações de Entrada -->
                <div class="p-3 space-y-2">
                    <form :action="'{{ route('products.openBox', ':id') }}'.replace(':id', p.id)" method="POST">
                        @csrf
                        <button type="submit" 
                            :disabled="!p.pai || p.pai.stock_quantity <= 0"
                            class="w-full bg-gray-900 text-white py-2 rounded-xl font-black text-[9px] uppercase hover:bg-blue-600 disabled:bg-gray-100 disabled:text-gray-300 transition-all flex items-center justify-center gap-2 italic">
                            <i data-lucide="package-open" class="w-3.5 h-3.5"></i>
                            Abrir Caixa
                        </button>
                    </form>
                </div>
            </div>
        </template>
    </div>

    <!-- Modal Entrada Manual (Adicionar Qtd) -->
    <div x-show="openAdjust" class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak x-transition>
        <div class="bg-white w-[500px] max-w-sm rounded-[2.5rem] shadow-2xl p-8" @click.away="openAdjust = false">
            <div class="text-center mb-6">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="plus-circle" class="w-6 h-6"></i>
                </div>
                <h3 class="text-lg font-black text-gray-800 uppercase italic leading-tight" x-text="selectedProduct.name"></h3>
                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-1">Adicionar stock diretamente</p>
            </div>

            <form :action="'{{ route('products.addShelf', ':id') }}'.replace(':id', selectedProduct.id)" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-[9px] font-black text-gray-400 uppercase block mb-1">Quantidade a adicionar (unidades)</label>
                    <input type="number" name="quantity" min="1" value="1" required
                        class="w-full bg-gray-50 border-none rounded-xl p-4 text-center text-xl font-black text-blue-600 focus:ring-2 focus:ring-blue-100">
                </div>
                
                <div class="flex gap-2">
                    <button type="button" @click="openAdjust = false" class="flex-1 bg-gray-100 text-gray-500 py-4 rounded-2xl font-black text-xs uppercase transition-all">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-[2] bg-blue-600 text-white py-4 rounded-2xl font-black text-xs uppercase shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">
                        Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Adicionar Novo Vínculo (Original) -->
    <div x-show="openAdd" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak x-transition>
        <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl p-10" @click.away="openAdd = false">
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-xl font-black text-gray-800 uppercase italic">Novo na Prateleira</h3>
                <button @click="openAdd = false" type="button"><i data-lucide="x" class="text-gray-400 hover:text-red-500"></i></button>
            </div>

            <form action="{{ route('products.storeShelf') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="text-[9px] font-black text-gray-400 uppercase block mb-1">Caixa de Origem (Pai)</label>
                    <select name="produto_pai_id" class="w-full bg-gray-50 border-none rounded-xl p-3 text-xs font-bold focus:ring-2 focus:ring-blue-100" required>
                        <option value="">Selecione...</option>
                        @foreach(\App\Models\Produto::whereNull('produto_pai_id')->get() as $pai)
                            <option value="{{ $pai->id }}">{{ $pai->name }} (SKU: {{ $pai->sku }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[9px] font-black text-gray-400 uppercase block mb-1">Unid. p/ Caixa</label>
                        <input type="number" name="fator_conversao" value="12" class="w-full bg-gray-50 border-none rounded-xl p-3 text-xs font-black focus:ring-1 focus:ring-blue-100">
                    </div>
                    <div>
                        <label class="text-[9px] font-black text-gray-400 uppercase block mb-1">Preço Venda</label>
                        <input type="number" step="0.01" name="sale_price" class="w-full bg-gray-50 border-none rounded-xl p-3 text-xs font-black text-blue-600 focus:ring-1 focus:ring-blue-100" required>
                    </div>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">
                    Vincular Avulso
                </button>
            </form>
        </div>
    </div>
</div>
@endsection