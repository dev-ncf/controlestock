@extends('layouts.app')

@section('title', 'Terminal de Vendas')

@section('content')
<!-- Inicializamos o Alpine com os produtos e o controle do nome da mesa -->
<div class="w-full mx-auto animate-in fade-in duration-500"
     x-data="{
        search: '',
        showNaming: false,
        allProducts: {{ $products->toJson() }},
        get filteredProducts() {
            if (this.search.trim() === '') return this.allProducts.slice(0, 15);
            return this.allProducts.filter(p =>
                p.name.toLowerCase().includes(this.search.toLowerCase()) ||
                p.sku.toLowerCase().includes(this.search.toLowerCase())
            ).slice(0, 15);
        },
        calculateVirtualStock(p) {
            let units = parseInt(p.stock_quantity);
            let fromBoxes = p.produto_pai_id && p.pai ? (parseInt(p.pai.stock_quantity) * parseInt(p.fator_conversao)) : 0;
            return units + fromBoxes;
        }
     }">

    <div class="grid grid-cols-12 gap-6 items-start">

        <!-- 1. COLUNA ESQUERDA: CATÁLOGO DE PRODUTOS -->
        <div class="col-span-12 lg:col-span-7 space-y-4">
            <div class="bg-white p-5 rounded-[1.5rem] border border-gray-200 shadow-sm min-h-[650px]">

                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-xs font-black text-blue-600 uppercase tracking-widest italic">Catálogo</h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase">Busca instantânea ativa</p>
                    </div>
                    <div class="relative w-1/2">
                        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-2.5 text-gray-400"></i>
                        <input type="text" x-model="search"
                            placeholder="Digite nome ou SKU..."
                            class="w-full pl-10 pr-4 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-blue-100 transition-all">
                    </div>
                </div>

                <!-- Grid de Produtos -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    <template x-for="p in filteredProducts" :key="p.id">
                        <div class="group bg-gray-50 rounded-[1.25rem] border-2 border-transparent hover:border-blue-500 hover:bg-white hover:shadow-xl transition-all overflow-hidden relative">
                            <form action="{{ route('invoices.addItem') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" :value="p.id">

                                <button type="submit" class="w-full text-left p-4 pb-0">
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="text-[8px] bg-white border border-gray-100 text-gray-400 px-1.5 py-0.5 rounded-md font-bold uppercase" x-text="p.sku"></span>
                                        <span class="text-[9px] font-black" :class="calculateVirtualStock(p) <= 5 ? 'text-red-500' : 'text-green-600'">
                                            <span x-text="calculateVirtualStock(p)"></span> un disp.
                                        </span>
                                    </div>
                                    <h4 class="font-black text-gray-800 text-[11px] leading-tight mb-4 h-8 overflow-hidden line-clamp-2 uppercase" x-text="p.name"></h4>
                                    <p class="text-blue-600 font-black text-sm mb-3">MZN <span x-text="parseFloat(p.sale_price).toLocaleString()"></span></p>
                                </button>

                                <div class="p-3 pt-0 flex gap-2">
                                    <input type="number" name="quantity" value="1" min="1" :max="calculateVirtualStock(p)"
                                        class="flex-1 bg-white border border-gray-200 rounded-lg text-center text-xs font-bold h-8 focus:ring-0">
                                    <button type="submit" class="bg-blue-600 text-white px-3 rounded-lg hover:bg-blue-700 shadow-md">
                                        <i data-lucide="shopping-cart" class="w-3.5 h-3.5" title="Adicionar ao carrinho" aria-label="Adicionar ao carrinho">+</i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- 2. COLUNA DIREITA: MESAS, CARRINHO E CHECKOUT -->
        <div class="col-span-12 lg:col-span-5 space-y-6">

            <!-- BARRA DE CONTAS ABERTAS / MESAS -->
            <div class="bg-white p-4 rounded-[1.5rem] border border-gray-200 shadow-sm overflow-hidden">
                <div class="flex items-center gap-2 overflow-x-auto pb-2 custom-scrollbar">
                    @foreach($multiCarts as $id => $data)
                        <div class="relative group flex-shrink-0">
                            <a href="{{ route('invoices.switchCart', $id) }}"
                               class="flex items-center gap-3 px-4 py-2.5 rounded-xl border-2 transition-all {{ $activeCartId == $id ? 'border-blue-600 bg-blue-50 text-blue-600 shadow-sm' : 'border-transparent bg-gray-50 text-gray-400' }}">
                                <span class="text-[10px] font-black uppercase tracking-widest">{{ $data['label'] }}</span>
                                @if(count($data['items']) > 0)
                                    <span class="bg-blue-600 text-white text-[8px] px-1.5 py-0.5 rounded-md font-black">{{ count($data['items']) }}</span>
                                @endif
                            </a>
                            @if(count($multiCarts) > 1)
                                <a href="{{ route('invoices.deleteCart', $id) }}" onclick="return confirm('Fechar esta mesa?')" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i data-lucide="x" class="w-2 h-2"></i>
                                </a>
                            @endif
                        </div>
                    @endforeach

                    <!-- Botão Novo Carrinho / Mesa -->
                    <div class="flex items-center gap-2">
                        <button x-show="!showNaming" @click="showNaming = true" type="button" class="p-2.5 bg-gray-900 text-white rounded-xl hover:bg-black transition-all">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                        </button>
                        <form x-show="showNaming" action="{{ route('invoices.newCart') }}" method="POST" class="flex items-center gap-2" @click.away="showNaming = false">
                            @csrf
                            <input type="text" name="label" placeholder="Nome da mesa..." class="border-none bg-gray-100 rounded-xl px-3 py-2 text-[10px] font-bold w-32 uppercase focus:ring-1 focus:ring-blue-500">
                            <button type="submit" class="bg-blue-600 text-white p-2 rounded-xl"><i data-lucide="check" class="w-3 h-3"></i></button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- CARRINHO -->
            <div class="bg-white rounded-[1.5rem] border border-gray-200 shadow-sm overflow-hidden flex flex-col h-[350px]">
                <div class="px-6 py-4 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest italic">Conta Selecionada</h3>
                    <span class="text-[10px] bg-slate-800 text-white px-2.5 py-1 rounded-lg font-bold">MZN {{ number_format($total, 2) }}</span>
                </div>
                <div class="flex-1 overflow-y-auto custom-scrollbar">
                    <table class="w-full text-left text-xs">
                        <!-- Substitua o conteúdo do <tbody> do CARRINHO por este: -->
                        <tbody class="divide-y divide-gray-50">
                            @forelse($cart as $index => $item)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-800 text-[11px]">{{ $item['name'] }}</p>
                                </td>

                                <!-- COLUNA EDITÁVEL -->
                                <td colspan="2" class="px-2 py-4">
                                    <form action="{{ route('invoices.updateItem', $index) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')

                                        <div class="flex flex-col">
                                            <span class="text-[7px] font-black text-gray-400 uppercase">Preço (MZN)</span>
                                            <input type="number" name="unit_price" step="0.01"
                                                value="{{ $item['unit_price'] }}"
                                                class="w-20 bg-gray-50 border border-gray-200 rounded px-1.5 py-1 text-[10px] font-black focus:ring-1 focus:ring-blue-500">
                                        </div>

                                        <div class="flex flex-col">
                                            <span class="text-[7px] font-black text-gray-400 uppercase">Qtd</span>
                                            <input type="number" name="quantity"
                                                value="{{ $item['quantity'] }}"
                                                class="w-12 bg-gray-50 border border-gray-200 rounded px-1.5 py-1 text-[10px] font-black focus:ring-1 focus:ring-blue-500">
                                        </div>

                                        <button type="submit" class="mt-3 text-blue-600 hover:text-blue-800 transition-transform active:scale-90">
                                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </td>

                                <td class="px-6 py-4 text-right font-black text-[11px] text-gray-700">
                                    {{ number_format($item['subtotal'], 2) }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('invoices.removeItem', $index) }}" class="text-red-300 hover:text-red-500">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-20 text-center text-gray-300 italic text-[10px] uppercase tracking-widest">
                                    Aguardando itens...
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- CHECKOUT -->
            <div class="bg-white p-6 rounded-[1.5rem] border border-gray-200 shadow-2xl"
                 x-data="{ status: 'paid', initialPayment: 0 }">
                <form action="{{ route('invoices.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <select name="customer_id" class="w-full bg-gray-50 border-none rounded-xl p-3 text-xs font-bold focus:ring-2 focus:ring-blue-100" required>
                            <option value="">Cliente...</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <select name="status" x-model="status" class="w-full bg-gray-50 border-none rounded-xl p-3 text-xs font-bold">
                            <option value="paid">Pronto Pagamento</option>
                            <option value="unpaid">Venda a Prazo</option>
                        </select>
                    </div>

                    <!-- Campos de Entrada e Vencimento (Dívida) -->
                    <div x-show="status == 'unpaid'" x-transition class="p-4 bg-blue-50 rounded-2xl border border-blue-100 grid grid-cols-2 gap-3">
                        <input type="number" name="amount_paid" x-model="initialPayment" placeholder="Valor Entrada" class="bg-white border-none rounded-lg p-2 text-xs font-bold">
                        <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="bg-white border-none rounded-lg p-2 text-xs font-bold">
                    </div>

                    <div class="bg-blue-600 px-8 py-6 rounded-[2rem] text-white w-full shadow-xl shadow-blue-100 relative overflow-hidden group">
                        <div class="absolute -right-4 -top-4 opacity-10 group-hover:scale-110 transition-transform">
                            <i data-lucide="shopping-cart" class="w-24 h-24" title="+" aria-label="+">+</i>
                        </div>
                        <p class="text-[10px] font-bold uppercase text-blue-100 mb-1 tracking-widest opacity-80 italic">Total desta Conta</p>
                        <p class="text-3xl font-black italic tracking-tighter">MZN {{ number_format($total, 2) }}</p>
                    </div>

                    <button type="submit" class="w-full bg-gray-900 text-white py-5 rounded-[1.5rem] font-black text-xs uppercase tracking-widest hover:bg-black transition-all shadow-2xl active:scale-95">
                        Finalizar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
