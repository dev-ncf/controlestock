@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto animate-in fade-in duration-700">

    <!-- HEADER DA PÁGINA -->
    <header class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-800 tracking-tighter uppercase italic">Stock de Produtos</h1>
            <p class="text-sm text-gray-500 font-medium">Gestão centralizada de inventário e preços.</p>
        </div>
        <a href="{{ route('products.create') }}" class="bg-slate-900 hover:bg-blue-600 text-white px-6 py-4 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-lg active:scale-95 flex items-center gap-2">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            Novo Produto
        </a>
    </header>

    <!-- BARRA DE PESQUISA -->
    <div class="bg-white p-4 rounded-[2rem] border border-gray-100 shadow-sm mb-6">
        <div class="relative">
            <input type="text" id="real-time-search"
                placeholder="Pesquisar em todo o catálogo (nome, SKU ou categoria)..."
                class="w-full bg-gray-50 border-none rounded-xl p-4 pl-12 text-sm font-bold focus:ring-2 focus:ring-blue-100 transition-all"
            >
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                <i data-lucide="search" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- TABELA DE PRODUTOS -->
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-left border-separate border-spacing-0">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Produto</th>
                    <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Categoria</th>
                    <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Preço Venda</th>
                    <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Disponibilidade</th>
                    <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Ações</th>
                </tr>
            </thead>
            <!-- Usamos um ID específico para o corpo que será manipulado pelo JS -->
            <tbody id="products-table-body">
                @foreach($products as $product)
                <tr class="product-row group hover:bg-blue-50/30 transition-colors">
                    <td class="px-6 py-5 border-t border-gray-50">
                        <div class="flex flex-col">
                            <span class="font-black text-gray-800 uppercase italic tracking-tight">{{ $product->name }}</span>
                            <span class="text-[10px] font-mono text-gray-400 uppercase">SKU: {{ $product->sku }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-5 border-t border-gray-50 text-sm font-bold text-gray-500">
                        <span class="bg-gray-100 px-3 py-1 rounded-lg text-[10px] uppercase">{{ $product->categoria->name ?? 'Geral' }}</span>
                    </td>
                    <td class="px-6 py-5 border-t border-gray-50 text-right">
                        <div class="flex flex-col items-end">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">MZN</span>
                            <span class="font-black text-gray-900 text-lg tracking-tighter">{{ number_format($product->sale_price, 2) }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-5 border-t border-gray-50 text-center">
                        @if($product->stock_quantity <= $product->min_stock)
                            <div class="inline-flex items-center gap-1.5 bg-red-50 text-red-600 px-3 py-1.5 rounded-xl animate-pulse">
                                <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                                <span class="text-xs font-black uppercase">{{ $product->stock_quantity }}</span>
                            </div>
                        @else
                            <div class="inline-flex items-center gap-1.5 bg-green-50 text-green-600 px-3 py-1.5 rounded-xl font-black">
                                <span class="text-xs font-black uppercase">{{ $product->stock_quantity }}</span>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-5 border-t border-gray-50 text-center">
                        <a href="{{ route('products.edit', $product->id) }}"
                           class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-slate-900 hover:text-white transition-all shadow-sm">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- ESTADO VAZIO -->
        <div id="no-results" class="hidden flex-col items-center justify-center py-20 text-gray-400 italic">
            <i data-lucide="package-search" class="w-12 h-12 mb-4 opacity-20"></i>
            <p class="font-medium">Nenhum produto corresponde à sua pesquisa.</p>
        </div>
    </div>

    <!-- Paginação (Envolvida em um ID para esconder durante a busca) -->
    <div id="pagination-container" class="mt-4">
        {{ $products->links() }}
    </div>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('real-time-search');
        const tableBody = document.getElementById('products-table-body');
        const paginationContainer = document.getElementById('pagination-container');
        const noResults = document.getElementById('no-results');
        
        // Guardamos o HTML inicial (a página 1 vinda do $products)
        const initialTableHTML = tableBody.innerHTML;
        
        // Transformamos o $all_products do PHP para JSON no JS
        const allProducts = @json($all_products->load('categoria'));

        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();

            if (query === "") {
                // Se busca vazia, volta para a lista paginada original
                tableBody.innerHTML = initialTableHTML;
                paginationContainer.style.display = "block";
                noResults.style.display = "none";
                lucide.createIcons(); // Re-inicializa ícones
                return;
            }

            // Esconde a paginação durante a busca global
            paginationContainer.style.display = "none";

            // Filtra no array completo
            const filtered = allProducts.filter(p => {
                const name = p.name.toLowerCase();
                const sku = p.sku ? p.sku.toLowerCase() : '';
                const cat = p.categoria ? p.categoria.name.toLowerCase() : 'geral';
                return name.includes(query) || sku.includes(query) || cat.includes(query);
            });

            if (filtered.length === 0) {
                tableBody.innerHTML = "";
                noResults.style.display = "flex";
            } else {
                noResults.style.display = "none";
                renderResults(filtered);
            }
        });

        function renderResults(products) {
            tableBody.innerHTML = "";
            products.forEach(p => {
                const isLowStock = p.stock_quantity <= (p.min_stock || 0);
                const price = new Intl.NumberFormat('pt-MZ', { minimumFractionDigits: 2 }).format(p.sale_price);
                const editUrl = `{{ route('products.edit', ':id') }}`.replace(':id', p.id);

                const row = `
                    <tr class="product-row group hover:bg-blue-50/30 transition-colors">
                        <td class="px-6 py-5 border-t border-gray-50">
                            <div class="flex flex-col">
                                <span class="font-black text-gray-800 uppercase italic tracking-tight">${p.name}</span>
                                <span class="text-[10px] font-mono text-gray-400 uppercase">SKU: ${p.sku || '---'}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 border-t border-gray-50 text-sm font-bold text-gray-500">
                            <span class="bg-gray-100 px-3 py-1 rounded-lg text-[10px] uppercase">${p.categoria ? p.categoria.name : 'Geral'}</span>
                        </td>
                        <td class="px-6 py-5 border-t border-gray-50 text-right">
                            <div class="flex flex-col items-end">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">MZN</span>
                                <span class="font-black text-gray-900 text-lg tracking-tighter">${price}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 border-t border-gray-50 text-center">
                            <div class="inline-flex items-center gap-1.5 ${isLowStock ? 'bg-red-50 text-red-600 animate-pulse' : 'bg-green-50 text-green-600'} px-3 py-1.5 rounded-xl font-black">
                                ${isLowStock ? '<i data-lucide="alert-triangle" class="w-3 h-3"></i>' : ''}
                                <span class="text-xs font-black uppercase">${p.stock_quantity}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 border-t border-gray-50 text-center">
                            <a href="${editUrl}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-slate-900 hover:text-white transition-all shadow-sm">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                        </td>
                    </tr>
                `;
                tableBody.insertAdjacentHTML('beforeend', row);
            });
            lucide.createIcons(); // Garante que os ícones do Lucide apareçam nos novos elementos
        }
    });
</script>
@endpush
@endsection