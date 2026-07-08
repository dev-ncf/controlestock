@extends('layouts.app')

@section('content')
<!-- Container Alpine.js -->
<div x-data="{ modalReceber: false, modalDebito: false, modalEliminar: false }" class="grid grid-cols-12 gap-6 animate-in fade-in duration-500">

    <!-- Coluna Esquerda: Perfil do Cliente -->
    <div class="col-span-12 lg:col-span-4 space-y-6">
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-200 shadow-sm text-center">
            <div class="w-20 h-20 bg-blue-100 rounded-[2rem] flex items-center justify-center text-blue-600 mx-auto mb-4">
                <i data-lucide="user" class="w-10 h-10"></i>
            </div>
            <h2 class="text-xl font-black text-gray-800 uppercase">{{ $customer->name }}</h2>
            <p class="text-xs text-gray-400 font-bold tracking-widest mb-6">NIF: {{ $customer->nif ?? 'S/ NIF' }}</p>

            <div class="p-6 bg-red-50 rounded-[2rem] border border-red-100 mb-4">
                <p class="text-[10px] font-black text-red-400 uppercase mb-1">Dívida Total do Cliente</p>
                <p class="text-3xl font-black text-red-600 italic">MZN {{ number_format($customer->current_balance, 2) }}</p>
            </div>

            <div class="mb-8 px-4 py-2 bg-gray-50 rounded-xl inline-block text-[10px] text-gray-400 font-bold uppercase tracking-wider">
                Limite de Crédito: MZN {{ number_format($customer->credit_limit, 2) }}
            </div>

            <!-- Botões de Ação -->
            <div class="grid grid-cols-1 gap-3">
                <button @click="modalReceber = true" class="w-full bg-green-600 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-green-100 hover:bg-green-700 transition-all">
                    <i data-lucide="hand-coins" class="w-4 h-4 inline mr-2"></i> Receber Pagamento
                </button>

                <button @click="modalDebito = true" class="w-full bg-gray-800 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-gray-200 hover:bg-gray-900 transition-all">
                    <i data-lucide="file-plus" class="w-4 h-4 inline mr-2"></i> Registar Novo Débito
                </button>
            </div>
                        <!-- ADICIONE ESTE BOTÃO JUNTO AOS OUTROS NO CARD DA ESQUERDA -->
            <a href="{{ route('customers.extract', $customer->id) }}" class="w-full bg-blue-50 text-blue-600 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-100 transition-all text-center mt-3 block">
                <i data-lucide="file-text" class="w-4 h-4 inline mr-2"></i> Ver Extrato Completo
            </a>
             <!-- ABAIXO DOS BOTÕES DE RECEBIMENTO NO CARD DA ESQUERDA -->
            <div class="pt-4 border-t border-gray-100 mt-4">
                <button @click="modalEliminar = true" class="flex items-center justify-center w-full text-red-400 hover:text-red-600 text-[10px] font-black uppercase tracking-widest transition-all group">
                    <i data-lucide="trash-2" class="w-3 h-3 mr-2 group-hover:animate-bounce"></i> Eliminar Cliente
                </button>
            </div>
        </div>
    </div>

    <!-- Coluna Direita: Histórico de Faturas -->
    <div class="col-span-12 lg:col-span-8">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-700">Histórico de Compras e Dívidas</h3>
            </div>

            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-[10px] uppercase font-bold text-gray-600">
                        <th class="p-4">Fatura</th>
                        <th class="p-4 text-right">Total</th>
                        <th class="p-4 text-right">Pago</th>
                        <th class="p-4 text-right">Falta</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-center">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($customer->vendas as $venda)
                        @php
                            $valorPago = $venda->pagamentos->sum('amount_paid');
                            $valorFalta = $venda->total_amount - $valorPago;
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4">
                                <div class="font-bold text-blue-600 text-sm">{{ $venda->invoice_number }}</div>
                                <div class="text-[10px] text-gray-400">{{ $venda->date->format('d/m/Y') }}</div>
                            </td>
                            <td class="p-4 text-right text-sm">MZN {{ number_format($venda->total_amount, 2) }}</td>
                            <td class="p-4 text-right text-sm text-green-600">MZN {{ number_format($valorPago, 2) }}</td>
                            <td class="p-4 text-right text-sm font-bold text-red-600">
                                {{ $valorFalta > 0 ? 'MZN ' . number_format($valorFalta, 2) : '-' }}
                            </td>
                            <td class="p-4 text-center">
                                @if($venda->status == 'paid' || $valorFalta <= 0)
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-[10px] font-black uppercase">Pago</span>
                                @elseif($valorPago > 0 && $valorFalta > 0)
                                    <span class="bg-orange-100 text-orange-700 px-2 py-1 rounded text-[10px] font-black uppercase">Parcial</span>
                                @else
                                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-[10px] font-black uppercase">Dívida</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- BOTÃO VER DETALHES (Show) -->
                                    <a href="{{ route('invoices.show', $venda->id) }}"
                                    class="bg-gray-100 text-gray-600 p-2 rounded hover:bg-gray-200 transition-all shadow-sm group"
                                    title="Ver Detalhes">
                                        <i data-lucide="eye" class="w-4 h-4 group-hover:scale-110"></i>
                                    </a>

                                    <!-- BOTÃO PAGAR -->
                                    @if($valorFalta > 0)
                                        <a href="{{ route('receipts.create', $venda->id) }}"
                                        class="bg-blue-600 text-white px-3 py-1.5 rounded text-[10px] font-black uppercase tracking-wider hover:bg-blue-700 transition shadow-sm active:scale-95">
                                            PAGAR
                                        </a>
                                    @else
                                        <span class="text-gray-300 text-[10px] font-bold uppercase italic px-2">Liquidado</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL: RECEBER PAGAMENTO (Diminuir Saldo Devedor) -->
    <div x-show="modalReceber" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" x-cloak>
        <div @click.away="modalReceber = false" class="bg-white rounded-[2.5rem] w-full max-w-md overflow-hidden animate-in zoom-in duration-300">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-black text-gray-800 uppercase tracking-tighter text-xl">Receber Valor</h3>
                    <button @click="modalReceber = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x"></i></button>
                </div>
                <form action="{{ route('customers.receive-payment') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Valor Entregue (MZN)</label>
                        <input type="number" name="amount" step="0.01" max="{{ abs($customer->current_balance) }}" required
                               class="w-full bg-gray-50 border-none rounded-2xl p-4 text-lg font-black focus:ring-2 focus:ring-green-100 mt-1"
                               placeholder="0.00">
                    </div>
                    <button type="submit" class="w-full bg-green-600 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-green-100 hover:bg-green-700">
                        Confirmar Entrada
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: REGISTAR NOVO DÉBITO (Aumentar Saldo Devedor) -->
    <div x-show="modalDebito" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" x-cloak>
        <div @click.away="modalDebito = false" class="bg-white rounded-[2.5rem] w-full max-w-md overflow-hidden animate-in zoom-in duration-300">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-black text-gray-800 uppercase tracking-tighter text-xl">Novo Débito Manual</h3>
                    <button @click="modalDebito = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x"></i></button>
                </div>
                <form action="{{ route('customers.add-debt') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Valor do Débito (MZN)</label>
                        <input type="number" name="amount" step="0.01" required
                               class="w-full bg-gray-50 border-none rounded-2xl p-4 text-lg font-black focus:ring-2 focus:ring-gray-200 mt-1"
                               placeholder="0.00">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Referência</label>
                        <input type="text" name="note" required
                               class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-gray-200 mt-1"
                               placeholder="Ex: Ajuste de conta, Venda externa...">
                    </div>
                    <button type="submit" class="w-full bg-gray-800 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-900">
                        Registar Débito
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
                <h3 class="font-black text-gray-800 uppercase text-lg mb-2">Confirmar Exclusão</h3>
                <p class="text-gray-500 text-xs font-medium leading-relaxed mb-6">
                    Deseja apagar o cliente <strong>{{ $customer->name }}</strong>? Todas as faturas e registos vinculados serão afetados.
                </p>

                <div class="space-y-3">
                    <form action="{{ route('customers.destroy', $customer->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-500 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-red-700 shadow-lg shadow-red-100">
                            Confirmar Eliminação
                        </button>
                    </form>
                    <button @click="modalEliminar = false" class="w-full bg-gray-100 text-gray-500 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-200">
                        Voltar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js & Icons -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<style> [x-cloak] { display: none !important; } </style>
@endsection
