@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Contas a Receber (Dívidas)</h2>

    <form action="{{ route('receipts.index') }}" method="GET" class="mb-6 flex gap-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar cliente ou fatura..." class="flex-1 border-gray-300 rounded-lg">
        <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg">Pesquisar</button>
    </form>

    <table class="w-full text-left">
        <thead>
            <tr class="bg-gray-50 border-b">
                <th class="p-4 text-xs font-bold text-gray-500 uppercase">Vencimento</th>
                <th class="p-4 text-xs font-bold text-gray-500 uppercase">Fatura</th>
                <th class="p-4 text-xs font-bold text-gray-500 uppercase">Cliente</th>
                <th class="p-4 text-right text-xs font-bold text-gray-500 uppercase">Valor Total</th>
                <th class="p-4 text-right text-xs font-bold text-gray-500 uppercase">Falta Pagar</th>
                <th class="p-4 text-center text-xs font-bold text-gray-500 uppercase">Ação</th>
            </tr>
        </thead>
        <tbody>
            @foreach($debts as $debt)
            <tr class="border-b hover:bg-gray-50">
                <td class="p-4 text-sm {{ $debt->due_date->isPast() ? 'text-red-600 font-bold' : '' }}">
                    {{ $debt->due_date->format('d/m/Y') }}
                </td>
                <td class="p-4 font-mono">{{ $debt->invoice_number }}</td>
                <td class="p-4 font-medium">{{ $debt->cliente->name }}</td>
                <td class="p-4 text-right">MZN {{ number_format($debt->total_amount, 2) }}</td>
                <td class="p-4 text-right text-red-600 font-bold">
                    MZN {{ number_format($debt->total_amount - $debt->pagamentos->sum('amount_paid'), 2) }}
                </td>
                <td class="p-4 text-center">
                    <a href="{{ route('receipts.create', $debt->id) }}" class="bg-green-600 text-white px-4 py-1 rounded text-sm font-bold hover:bg-green-700">
                        PAGAR
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection