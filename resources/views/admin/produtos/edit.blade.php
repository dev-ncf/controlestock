@extends('layouts.app')

@section('title', 'Editar Produto')

@section('content')
<div class="max-w-4xl mx-auto animate-in fade-in duration-500">
    
    <header class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-black text-gray-800 tracking-tight uppercase italic">Editar Produto</h1>
            <p class="text-sm text-gray-500 font-medium">Atualize os dados técnicos e financeiros do item.</p>
        </div>
        <a href="{{ route('products.index') }}" class="flex items-center gap-2 text-gray-500 hover:text-gray-800 transition-all font-bold text-xs uppercase tracking-widest">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Voltar
        </a>
    </header>

    <form action="{{ route('products.update', $product->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <!-- Coluna Principal: Dados Gerais -->
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm space-y-6">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase block mb-2 tracking-widest">Nome do Produto</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" 
                            class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-100" required>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase block mb-2 tracking-widest">Código SKU</label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" 
                                class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-100" required>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase block mb-2 tracking-widest">Categoria</label>
                            <select name="categoria_id" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-100">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $product->categoria_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase block mb-2 tracking-widest">Stock Mínimo (Alerta)</label>
                        <input type="number" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}" 
                            class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-100">
                    </div>
                </div>

                <!-- SEÇÃO: RELAÇÃO CAIXA / AVULSO -->
                <div class="bg-blue-50 p-8 rounded-[2.5rem] border border-blue-100 space-y-6">
                    <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="layers" class="text-blue-600 w-5 h-5"></i>
                        <h3 class="text-xs font-black text-blue-600 uppercase tracking-widest">Configuração de Venda Fracionada</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] font-black text-blue-400 uppercase block mb-2 tracking-widest">Este produto é um Avulso de:</label>
                            <select name="produto_pai_id" class="w-full bg-white border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-200">
                                <option value="">Nenhum</option>
                                @foreach($all_products as $p)
                                    @if($p->id != $product->id)
                                        <option value="{{ $p->id }}" {{ $product->produto_pai_id == $p->id ? 'selected' : '' }}>
                                            {{ $p->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-blue-400 uppercase block mb-2 tracking-widest">Unidades por Caixa</label>
                            <input type="number" name="fator_conversao" value="{{ old('fator_conversao', $product->fator_conversao) }}" 
                                class="w-full bg-white border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-200">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna Lateral: Financeiro -->
            <div class="space-y-6">
                <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-xl text-white space-y-6">
                    <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest border-b border-slate-800 pb-4 italic">Financeiro</h3>

                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase block mb-2 tracking-widest">Preço de Compra (Custo)</label>
                        <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}"
                            class="w-full bg-slate-800 border-none rounded-2xl p-4 text-sm font-black text-blue-400 focus:ring-2 focus:ring-blue-500 shadow-inner">
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase block mb-2 tracking-widest">Preço de Venda</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-500 uppercase font-mono">MZN</span>
                            <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}"
                                class="w-full bg-slate-800 border-none rounded-2xl p-4 pl-14 text-2xl font-black text-green-400 focus:ring-2 focus:ring-green-500 shadow-inner" 
                                required>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-5 rounded-3xl font-black text-xs uppercase tracking-widest transition-all shadow-lg shadow-blue-900/20 active:scale-95">
                            Atualizar Produto
                        </button>
                    </div>
                </div>

                <div class="p-6 bg-white rounded-3xl border border-gray-100 shadow-sm italic text-gray-400 text-[10px] uppercase tracking-tight">
                    <div class="flex gap-3">
                        <i data-lucide="info" class="w-4 h-4 flex-shrink-0"></i>
                        <p>O lucro bruto é a diferença entre o preço de venda informado e o custo de compra.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection