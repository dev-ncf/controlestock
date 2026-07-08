@extends('layouts.app')

@section('content')
<!-- Container do Alpine.js para controlar os modais -->
<div x-data="{ modalDivida: false, modalPagamento: false,modalEliminar: false }" class="grid grid-cols-12 gap-8 animate-in fade-in duration-500">

    <!-- Coluna Esquerda: Perfil e Liquidação -->
    <div class="col-span-12 lg:col-span-4 space-y-6">
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-200 shadow-sm text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-[2rem] flex items-center justify-center text-gray-400 mx-auto mb-4">
                <i data-lucide="truck" class="w-10 h-10"></i>
            </div>
            <h2 class="text-xl font-black text-gray-800 uppercase">{{ $supplier->name }}</h2>
            <p class="text-xs text-gray-400 font-bold tracking-widest mb-6">NIF: {{ $supplier->nif ?? 'S/ NIF' }}</p>

            <div class="p-6 bg-red-50 rounded-[2rem] border border-red-100 mb-8">
                <p class="text-[10px] font-black text-red-400 uppercase mb-1">Dívida Atual conosco</p>
                <p class="text-3xl font-black text-red-600 italic">MZN {{ number_format($supplier->balance_to_pay, 2) }}</p>
            </div>

            <!-- Botões de Ação -->
            <div class="grid grid-cols-1 gap-3">
                <button @click="modalPagamento = true" class="w-full bg-green-600 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-green-100 hover:bg-green-700 transition-all">
                    <i data-lucide="arrow-down-circle" class="w-4 h-4 inline mr-2"></i> Pagar / Diminuir
                </button>

                <button @click="modalDivida = true" class="w-full bg-gray-800 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-gray-200 hover:bg-gray-900 transition-all">
                    <i data-lucide="plus-circle" class="w-4 h-4 inline mr-2"></i> Registar Nova Dívida
                </button>
            </div>
             <!-- ABAIXO DOS BOTÕES DE PAGAMENTO NO CARD DA ESQUERDA -->
            <div class="pt-4 border-t border-gray-100 mt-4">
                <button @click="modalEliminar = true" class="flex items-center justify-center w-full text-red-400 hover:text-red-600 text-[10px] font-black uppercase tracking-widest transition-all group">
                    <i data-lucide="trash-2" class="w-3 h-3 mr-2 group-hover:animate-bounce"></i> Eliminar Fornecedor
                </button>
            </div>
        </div>
    </div>

    <!-- Coluna Direita: Histórico de Compras -->
    <div class="col-span-12 lg:col-span-8 bg-white rounded-[2.5rem] border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/30">
            <h3 class="text-xs font-black text-gray-700 uppercase tracking-widest italic">Histórico de Entradas de Stock</h3>
        </div>
        <table class="w-full text-left">
            <thead class="bg-white text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">
                <tr>
                    <th class="px-8 py-4">Data</th>
                    <th class="px-8 py-4">Produto</th>
                    <th class="px-8 py-4 text-center">Quantidade</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($history as $item)
                <tr>
                    <td class="px-8 py-5 text-xs text-gray-500 font-mono">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-8 py-5 font-bold text-gray-800 text-sm">{{ $item->produto->name }}</td>
                    <td class="px-8 py-5 text-center">
                        <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-black">+ {{ $item->quantity }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- MODAL: REGISTAR NOVA DÍVIDA -->
    <div x-show="modalDivida" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" x-cloak>
        <div @click.away="modalDivida = false" class="bg-white rounded-[2.5rem] w-full max-w-md overflow-hidden animate-in zoom-in duration-300">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-black text-gray-800 uppercase tracking-tighter text-xl">Nova Dívida</h3>
                    <button @click="modalDivida = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x"></i></button>
                </div>
                <form action="{{ route('suppliers.add-debt') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="fornecedor_id" value="{{ $supplier->id }}">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Valor da Dívida (MZN)</label>
                        <input type="number" name="amount" step="0.01" required
                               class="w-full bg-gray-50 border-none rounded-2xl p-4 text-lg font-black focus:ring-2 focus:ring-gray-200 mt-1"
                               placeholder="0.00">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Motivo/Referência</label>
                        <input type="text" name="reference"
                               class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-gray-200 mt-1"
                               placeholder="Ex: Compra a prazo, Ajuste...">
                    </div>
                    <button type="submit" class="w-full bg-red-500 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-red-100 hover:bg-red-700">
                        Confirmar Registo
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: PAGAMENTO / DIMINUIR DÍVIDA -->
    <div x-show="modalPagamento" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" x-cloak>
        <div @click.away="modalPagamento = false" class="bg-white rounded-[2.5rem] w-full max-w-md overflow-hidden animate-in zoom-in duration-300">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-black text-gray-800 uppercase tracking-tighter text-xl">Efectuar Pagamento</h3>
                    <button @click="modalPagamento = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x"></i></button>
                </div>
                <div class="mb-6 p-4 bg-green-50 rounded-2xl border border-green-100 text-center">
                    <p class="text-[10px] font-black text-green-400 uppercase">Saldo Devedor</p>
                    <p class="text-xl font-black text-green-700 italic">MZN {{ number_format($supplier->balance_to_pay, 2) }}</p>
                </div>
                <form action="{{ route('suppliers.pay') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="fornecedor_id" value="{{ $supplier->id }}">

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase ml-2">
                            Valor a Pagar (MZN)
                        </label>
                        <input
                            type="number"
                            name="amount"
                            step="0.01"
                            min="0.01"
                            max="{{ abs($supplier->balance_to_pay) }}"
                            required
                            class="w-full bg-gray-50 border-none rounded-2xl p-4 text-lg font-black focus:ring-2 focus:ring-green-100 mt-1"
                            placeholder="0.00"
                        >
                        <p class="text-[10px] text-gray-400 mt-1 ml-2">
                            Limite máximo: MZN {{ number_format(abs($supplier->balance_to_pay), 2) }}
                        </p>
                    </div>

                    <button type="submit" class="w-full bg-green-600 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-green-100 hover:bg-green-700">
                        Confirmar Pagamento
                    </button>
                </form>
            </div>
        </div>
    </div>
     <!-- NO FINAL DO ARQUIVO: MODAL DE CONFIRMAÇÃO DE ELIMINAÇÃO -->
    <div x-show="modalEliminar" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-red-900/20 backdrop-blur-md" x-cloak>
        <div @click.away="modalEliminar = false" class="bg-white rounded-[2.5rem] w-500 max-w-sm overflow-hidden shadow-2xl animate-in zoom-in duration-300 border border-red-100">
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="alert-triangle" class="w-8 h-8"></i>
                </div>
                <h3 class="font-black text-gray-800 uppercase text-lg mb-2">Tens a certeza?</h3>
                <p class="text-gray-500 text-xs font-medium leading-relaxed mb-6">
                    Esta ação irá remover permanentemente o fornecedor <strong>{{ $supplier->name }}</strong> e todo o seu histórico. Esta ação não pode ser desfeita.
                </p>

                <div class="space-y-3">
                    <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-500 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-red-700 shadow-lg shadow-red-100">
                            Sim, Eliminar Definitivamente
                        </button>
                    </form>
                    <button @click="modalEliminar = false" class="w-full bg-gray-100 text-gray-500 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-200">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Script necessário para o Alpine.js e Lucide Icons se não estiverem no seu Layout -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<style> [x-cloak] { display: none !important; } </style>
@endsection
