<div class="p-8 max-w-5xl mx-auto">
    
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800">Nova Venda</h2>
        <input type="text" wire:model.live="search" 
               placeholder="Pesquisar produto ou ler código de barras..." 
               class="w-full mt-4 p-4 bg-white border-2 border-blue-100 rounded-2xl focus:border-blue-500 outline-none shadow-sm transition-all">
    </div>

    <div class="grid grid-cols-12 gap-8">
        
        <!-- LADO ESQUERDO: LISTA DE PRODUTOS DISPONÍVEIS -->
        <div class="col-span-12 lg:col-span-7">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">
                {{ $isSearch ? 'Resultados da Pesquisa' : 'Sugestões de Stock' }}
            </h3>

            <div class="grid grid-cols-1 gap-3">
                @forelse($products as $p)
                    <button wire:click="addItem({{ $p->id }})" 
                            class="flex justify-between items-center p-4 bg-white border border-gray-100 rounded-2xl shadow-sm hover:border-blue-500 hover:shadow-md transition-all group">
                        <div class="text-left">
                            <p class="font-bold text-gray-800 group-hover:text-blue-600">{{ $p->name }}</p>
                            <p class="text-xs text-gray-400 font-medium">Stock: {{ $p->stock_quantity }} un</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-black text-gray-900">MZN {{ number_format($p->sale_price, 2) }}</p>
                            <span class="text-[10px] text-blue-500 font-bold uppercase">+ Adicionar</span>
                        </div>
                    </button>
                @empty
                    <div class="p-10 text-center bg-gray-50 rounded-2xl border-2 border-dashed">
                        <p class="text-gray-400 italic">Nenhum produto encontrado...</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- LADO DIREITO: CARRINHO -->
        <div class="col-span-12 lg:col-span-5">
            <div class="bg-gray-900 p-6 rounded-[2rem] shadow-xl sticky top-6">
                <h3 class="text-white font-bold mb-6 flex justify-between items-center">
                    <span>Carrinho</span>
                    <span class="bg-blue-600 text-[10px] px-2 py-1 rounded-lg">{{ count($cart) }} itens</span>
                </h3>

                <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2">
                    @forelse($cart as $item)
                        <div class="flex justify-between items-center bg-white/5 p-3 rounded-xl border border-white/10">
                            <div class="text-white">
                                <p class="text-sm font-bold">{{ $item['name'] }}</p>
                                <p class="text-[10px] text-gray-400">1 x MZN {{ number_format($item['price'], 2) }}</p>
                            </div>
                            <p class="text-white font-black text-sm">MZN {{ number_format($item['price'], 2) }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-10 italic">O carrinho está vazio</p>
                    @endforelse
                </div>

                <div class="mt-8 pt-6 border-t border-white/10">
                    <div class="flex justify-between text-gray-400 mb-4">
                        <span>Total a Pagar</span>
                        <span class="text-white font-black text-2xl italic">MZN {{ number_format(collect($cart)->sum('price'), 2) }}</span>
                    </div>
                    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-bold transition-all shadow-lg shadow-blue-900/20">
                        FINALIZAR VENDA
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>