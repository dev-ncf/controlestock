@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Cabeçalho de Ações -->
    <div class="flex justify-between items-center">
        <a href="{{ route('invoices.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i> Voltar ao Histórico
        </a>
        <div class="space-x-2">
            <!-- Botão A4 -->
            <a href="{{ route('invoices.print', $invoice->id) }}" target="_blank" class="bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-bold">
                <i class="fas fa-file-invoice mr-2"></i> IMPRIMIR A4
            </a>

            <!-- Botão Talão 80mm -->
            <a href="{{ route('invoices.talao', $invoice->id) }}" target="_blank" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold">
                <i class="fas fa-print mr-2"></i> IMPRIMIR TALÃO
            </a>
            @if($remaining > 0)
                <a href="{{ route('receipts.create', $invoice->id) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-green-700">
                    <i class="fas fa-money-bill-wave mr-2"></i> Registar Pagamento
                </a>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <!-- Info da Fatura -->
        <div class="p-8 border-b border-gray-100 flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-black text-gray-800 uppercase italic">Fatura #{{ $invoice->invoice_number }}</h1>
                <p class="text-gray-500">Data de Emissão: {{ $invoice->date->format('d/m/Y H:i') }}</p>
                <div class="mt-4">
                    @if($remaining <= 0)
                        <span class="bg-green-100 text-green-700 px-4 py-1 rounded-full text-xs font-black uppercase">Totalmente Paga</span>
                    @elseif($totalPaid > 0)
                        <span class="bg-orange-100 text-orange-700 px-4 py-1 rounded-full text-xs font-black uppercase">Pagamento Parcial</span>
                    @else
                        <span class="bg-red-100 text-red-700 px-4 py-1 rounded-full text-xs font-black uppercase">Aguardando Pagamento</span>
                    @endif
                </div>
            </div>
            <div class="text-right">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Cliente</h3>
                <p class="text-xl font-bold text-gray-800">{{ $invoice->cliente->name }}</p>
                <p class="text-gray-500 text-sm">NIF: {{ $invoice->cliente->nif ?? 'S/ NIF' }}</p>
            </div>
        </div>

        <!-- Tabela de Itens -->
        <div class="p-0">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b">
                        <th class="p-6">Descrição do Produto</th>
                        <th class="p-6 text-center">Quantidade</th>
                        <th class="p-6 text-right">Preço Unitário</th>
                        <th class="p-6 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($invoice->items as $item)
                    <tr>
                        <td class="p-6">
                            <p class="font-bold text-gray-800">{{ $item->produto->name }}</p>
                            <p class="text-xs text-gray-400">SKU: {{ $item->produto->sku }}</p>
                        </td>
                        <td class="p-6 text-center text-gray-600">{{ $item->quantity }}</td>
                        <td class="p-6 text-right text-gray-600">MZN {{ number_format($item->unit_price, 2) }}</td>
                        <td class="p-6 text-right font-bold text-gray-800">MZN {{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Resumo Financeiro -->
        <div class="p-8 bg-gray-50 border-t flex justify-end">
            <div class="w-full md:w-1/3 space-y-3">
                <div class="flex justify-between text-gray-600">
                    <span>Total da Fatura:</span>
                    <span class="font-bold">MZN {{ number_format($invoice->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-green-600 font-medium">
                    <span>Total Pago:</span>
                    <span>- MZN {{ number_format($totalPaid, 2) }}</span>
                </div>
                <div class="flex justify-between text-xl font-black text-gray-800 pt-3 border-t border-gray-200">
                    <span>SALDO DEVEDOR:</span>
                    <span class="{{ $remaining > 0 ? 'text-red-600' : 'text-green-600' }}">
                        MZN {{ number_format($remaining, 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Histórico de Recibos (Se houver) -->
    @if($invoice->pagamentos->count() > 0)
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="font-bold text-gray-700 mb-4 uppercase text-xs">Histórico de Pagamentos (Recibos)</h3>
        <div class="space-y-3">
            @foreach($invoice->pagamentos as $payment)
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border border-gray-100">
                <div class="flex items-center space-x-4">
                    <div class="bg-green-100 text-green-600 p-2 rounded-full">
                        <i class="fas fa-check text-xs"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800">Pagamento em {{ $payment->payment_date }}</p>
                        <p class="text-[10px] text-gray-400 uppercase">{{ $payment->payment_method }} - {{ $payment->reference ?? 'Sem ref.' }}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <p class="font-bold text-green-600">MZN {{ number_format($payment->amount_paid, 2) }}</p>

                    <!-- BOTÃO ELIMINAR PAGAMENTO -->
                    <form action="{{ route('receipts.destroy', $payment->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja eliminar este pagamento? O saldo da fatura será atualizado.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-300 hover:text-red-600 transition-colors p-2">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
