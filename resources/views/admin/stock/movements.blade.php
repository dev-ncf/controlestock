@extends('layouts.app')

@section('content')
<!-- Container principal com Alpine.js para controlar o Modal -->
<div class="space-y-6" x-data="{ openAjuste: false }">
    
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800 uppercase italic">Histórico de Movimentações</h2>
        <div class="flex space-x-3">
             <button @click="openAjuste = true" class="bg-gray-900 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg hover:bg-black transition-all flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Registar Ajuste
             </button>
             <button onclick="window.print()" class="bg-white border border-gray-200 px-5 py-2.5 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all flex items-center gap-2">
                <i data-lucide="printer" class="w-4 h-4"></i> Imprimir
             </button>
        </div>
    </div>

    <!-- Filtros (Mantidos como o seu código) -->
    <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
        <form action="{{ route('stock.movements.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Produto</label>
                <select name="produto_id" class="w-full border-none bg-gray-50 rounded-xl p-3 text-xs font-bold focus:ring-2 focus:ring-blue-100">
                    <option value="">Todos os Produtos</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ request('produto_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Tipo de Fluxo</label>
                <select name="type" class="w-full border-none bg-gray-50 rounded-xl p-3 text-xs font-bold focus:ring-2 focus:ring-blue-100">
                    <option value="">Todos</option>
                    <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Entradas (+)</option>
                    <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Saídas (-)</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Período</label>
                <div class="flex gap-2">
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full border-none bg-gray-50 rounded-xl p-3 text-[10px] font-bold">
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full border-none bg-gray-50 rounded-xl p-3 text-[10px] font-bold">
                </div>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-700 transition shadow-lg shadow-blue-100">
                    Filtrar Histórico
                </button>
            </div>
        </form>
    </div>

    <!-- Tabela de Movimentações -->
    <div class="bg-white rounded-[2rem] border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                    <th class="p-6">Data / Hora</th>
                    <th class="p-6">Produto</th>
                    <th class="p-6 text-center">Tipo</th>
                    <th class="p-6 text-center">Quantidade</th>
                    <th class="p-6">Motivo da Operação</th>
                    <th class="p-6">Utilizador</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($movements as $m)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="p-6 text-xs text-gray-500 font-mono italic">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                    <td class="p-6">
                        <div class="font-bold text-gray-800 text-sm">{{ $m->produto->name }}</div>
                        <div class="text-[9px] text-gray-400 uppercase font-black">SKU: {{ $m->produto->sku }}</div>
                    </td>
                    <td class="p-6 text-center">
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase {{ $m->type == 'in' ? 'bg-blue-50 text-blue-600' : 'bg-red-50 text-red-600' }}">
                            {{ $m->type == 'in' ? 'Entrada' : 'Saída' }}
                        </span>
                    </td>
                    <td class="p-6 text-center font-black text-sm {{ $m->type == 'in' ? 'text-blue-600' : 'text-red-600' }}">
                        {{ $m->type == 'in' ? '+' : '-' }}{{ $m->quantity }}
                    </td>
                    <td class="p-6">
                        <p class="text-xs text-gray-600 font-medium">{{ $m->reason }}</p>
                    </td>
                    <td class="p-6">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 bg-gray-100 rounded-full flex items-center justify-center text-[10px] font-bold text-gray-400 uppercase">
                                {{ substr($m->user->name ?? 'A', 0, 1) }}
                            </div>
                            <span class="text-[10px] font-bold text-gray-500 uppercase">{{ $m->user->name ?? 'Admin' }}</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-20 text-center text-gray-300 italic text-sm">Nenhum registo encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-6 bg-gray-50 border-t border-gray-100">
            {{ $movements->links() }}
        </div>
    </div>

    <!-- MODAL DE AJUSTE (Powered by Alpine.js) -->
    <div x-show="openAjuste" 
         class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        
        <div class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden" @click.away="openAjuste = false">
            <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-xl font-black text-gray-800 uppercase italic tracking-tighter">Registar Ajuste Manual</h3>
                <button @click="openAjuste = false" class="text-gray-400 hover:text-red-500 transition-colors">
                    <i data-lucide="x-circle" class="w-6 h-6"></i>
                </button>
            </div>

            <form action="{{ route('stock.movements.ajuste') }}" method="POST" class="p-8 space-y-6">
                @csrf
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase block mb-2 tracking-widest">Produto</label>
                    <select name="produto_id" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-100" required>
                        <option value="">Selecione o produto...</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} (Stock: {{ $p->stock_quantity }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase block mb-2 tracking-widest">Operação</label>
                        <select name="type" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-red-100 text-red-600" required>
                            <option value="out">Dar Baixa (-)</option>
                            <option value="in">Dar Entrada (+)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase block mb-2 tracking-widest">Quantidade</label>
                        <input type="number" name="quantity" min="1" value="1" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-black focus:ring-2 focus:ring-blue-100" required>
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase block mb-2 tracking-widest">Motivo do Ajuste</label>
                    <select name="reason" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-100" required>
                        <option value="Produto Expirado / Validade">Produto Expirado / Validade</option>
                        <option value="Produto Quebrado / Danificado">Produto Quebrado / Danificado</option>
                        <option value="Erro de Inventário / Contagem">Erro de Inventário / Contagem</option>
                        <option value="Oferta / Brinde">Oferta / Brinde</option>
                        <option value="Uso Interno">Uso Interno</option>
                    </select>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-gray-900 text-white py-5 rounded-[1.5rem] font-black text-sm uppercase tracking-widest hover:bg-black transition-all shadow-xl shadow-gray-200">
                        Confirmar Ajuste
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection