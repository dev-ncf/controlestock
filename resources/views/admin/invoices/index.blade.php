@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Histórico de Vendas</h2>
            <p class="text-sm text-gray-500">Consulte todas as faturas emitidas e o seu estado.</p>
        </div>
        <a href="{{ route('invoices.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-cart-plus mr-2"></i> Nova Venda
        </a>
    </div>

    <!-- Alertas de Sucesso/Erro -->
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabela de Faturas -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Nº Fatura</th>
                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Data</th>
                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Cliente</th>
                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase text-right">Valor Total</th>
                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase text-center">Estado</th>
                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase text-center">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($invoices as $invoice)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-4 font-mono font-bold text-blue-600">
                        {{ $invoice->invoice_number }}
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-600">
                        {{ $invoice->date->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-4 py-4 text-sm font-medium">
                        {{ $invoice->cliente->name }}
                    </td>
                    <td class="px-4 py-4 text-right font-black text-gray-800">
                        MZN {{ number_format($invoice->total_amount, 2) }}
                    </td>
                    <td class="px-4 py-4 text-center">
                        @if($invoice->status == 'paid')
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold uppercase">Pago</span>
                        @elseif($invoice->status == 'unpaid')
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold uppercase">Dívida</span>
                        @else
                            <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold uppercase">Parcial</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-center space-x-2">
                        <!-- Botão para Imprimir PDF -->
                        <a href="{{ route('invoices.print', $invoice->id) }}" class="text-gray-500 hover:text-red-600" title="Imprimir PDF">
                            <i class="fas fa-file-pdf text-xl"></i>
                        </a>
                        
                        <!-- Botão para Detalhes (Opcional) -->
                        <a href="{{route('invoices.show',$invoice->id) }}" class="text-gray-500 hover:text-blue-600" title="Ver Detalhes">
                            <i class="fas fa-eye text-xl"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-10 text-center text-gray-400 italic">
                        Ainda não foram emitidas faturas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="mt-4">
        {{ $invoices->links() }}
    </div>
</div>
@endsection