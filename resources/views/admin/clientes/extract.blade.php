@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 animate-in fade-in duration-500">
    
    <header class="flex justify-between items-center bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-gray-800 uppercase italic">Extrato de Conta</h1>
            <p class="text-sm text-gray-500 font-medium">Cliente: <strong>{{ $customer->name }}</strong></p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('customers.extractPdf', $customer->id) }}" target="_blank" class="bg-gray-900 text-white px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest flex items-center gap-2">
                <i data-lucide="download-cloud" class="w-4 h-4"></i> Exportar PDF
            </a>
            <a href="{{ route('customers.show', $customer->id) }}" class="bg-gray-100 text-gray-500 px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest">Voltar</a>
        </div>
    </header>

    <div class="bg-white rounded-[2.5rem] border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                <tr>
                    <th class="p-6">Data</th>
                    <th class="p-6">Descrição / Ref</th>
                    <th class="p-6 text-right">Débito (+)</th>
                    <th class="p-6 text-right">Crédito (-)</th>
                    <th class="p-6 text-right">Saldo Acumulado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @php $runningBalance = 0; @endphp
                @foreach($ledger as $item)
                    @php 
                        $runningBalance += ($item->debit - $item->credit);
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-6 text-xs text-gray-500 font-mono">{{ $item->date }}</td>
                        <td class="p-6">
                            <span class="text-xs font-bold text-gray-800 uppercase">{{ $item->ref }}</span>
                        </td>
                        <td class="p-6 text-right text-sm font-bold text-red-600">
                            {{ $item->debit > 0 ? 'MZN ' . number_format($item->debit, 2) : '-' }}
                        </td>
                        <td class="p-6 text-right text-sm font-bold text-green-600">
                            {{ $item->credit > 0 ? 'MZN ' . number_format($item->credit, 2) : '-' }}
                        </td>
                        <td class="p-6 text-right text-sm font-black text-gray-900">
                            MZN {{ number_format($runningBalance, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="p-8 bg-slate-900 text-white flex justify-between items-center">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Saldo Final Devedor</p>
            <p class="text-3xl font-black italic tracking-tighter text-red-400">MZN {{ number_format($customer->current_balance, 2) }}</p>
        </div>
    </div>
</div>
@endsection