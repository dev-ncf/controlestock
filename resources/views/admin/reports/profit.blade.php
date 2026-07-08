@extends('layouts.app')

@section('title', 'Relatório de Lucratividade')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    
    <!-- Cabeçalho com Filtros Rápidos -->
    <div class="bg-white p-6 rounded-[2rem] border border-gray-200 shadow-sm space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-black text-gray-800 uppercase italic">Relatório de Lucro</h1>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Análise de performance e margens</p>
            </div>
            
            <!-- Botões de Período Rápido -->
            <div class="flex flex-wrap gap-2">
                @php
                    $today = \Carbon\Carbon::today()->format('Y-m-d');
                    $startWeek = \Carbon\Carbon::now()->startOfWeek()->format('Y-m-d');
                    $startMonth = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
                    $startYear = \Carbon\Carbon::now()->startOfYear()->format('Y-m-d');
                @endphp

                <button onclick="setPeriod('{{ $today }}', '{{ $today }}')" 
                    class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $start == $today ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'bg-gray-50 text-gray-400 hover:bg-gray-100' }}">
                    Hoje
                </button>
                <button onclick="setPeriod('{{ $startWeek }}', '{{ $today }}')" 
                    class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $start == $startWeek ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'bg-gray-50 text-gray-400 hover:bg-gray-100' }}">
                    Semanal
                </button>
                <button onclick="setPeriod('{{ $startMonth }}', '{{ $today }}')" 
                    class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $start == $startMonth ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'bg-gray-50 text-gray-400 hover:bg-gray-100' }}">
                    Mensal
                </button>
                <button onclick="setPeriod('{{ $startYear }}', '{{ $today }}')" 
                    class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $start == $startYear ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'bg-gray-50 text-gray-400 hover:bg-gray-100' }}">
                    Anual
                </button>
            </div>
        </div>

        <div class="h-px bg-gray-50 w-full"></div>

        <!-- Formulário de Data Manual -->
        <form id="filterForm" action="{{ route('reports.profit') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="text-[10px] font-black text-gray-400 uppercase block mb-2 tracking-widest">Data Inicial</label>
                <input type="date" name="start_date" id="start_date" value="{{ $start }}" class="w-full bg-gray-50 border-none rounded-xl p-3 text-xs font-bold focus:ring-2 focus:ring-blue-100">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="text-[10px] font-black text-gray-400 uppercase block mb-2 tracking-widest">Data Final</label>
                <input type="date" name="end_date" id="end_date" value="{{ $end }}" class="w-full bg-gray-50 border-none rounded-xl p-3 text-xs font-bold focus:ring-2 focus:ring-blue-100">
            </div>
            <button type="submit" class="bg-gray-900 text-white px-8 py-3.5 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-black transition-all shadow-lg flex items-center gap-2">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                Filtrar
            </button>
        </form>
    </div>
    <div class="flex justify-end mb-4">
    <a href="{{ request()->fullUrlWithQuery(['download' => 1]) }}" 
       class="bg-red-500 text-white px-6 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest flex items-center gap-2 hover:bg-red-700 transition-all shadow-lg shadow-red-100">
        <i data-lucide="file-text" class="w-4 h-4"></i> Baixar PDF
    </a>
</div>

    <!-- Cards de KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Receita Bruta -->
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm hover:translate-y-[-4px] transition-all">
            <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 mb-6">
                <i data-lucide="trending-up" class="w-6 h-6"></i>
            </div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Faturação Total</p>
            <p class="text-3xl font-black text-gray-800 tracking-tighter">MZN {{ number_format($stats->total_revenue ?? 0, 2) }}</p>
        </div>

        <!-- Custo das Mercadorias -->
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm hover:translate-y-[-4px] transition-all">
            <div class="w-12 h-12 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-600 mb-6">
                <i data-lucide="shopping-bag" class="w-6 h-6"></i>
            </div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Custo de Compra (CPV)</p>
            <p class="text-3xl font-black text-gray-800 tracking-tighter">MZN {{ number_format($stats->total_cost ?? 0, 2) }}</p>
        </div>

        <!-- Lucro Real -->
        <div class="bg-gradient-to-br from-green-500 to-green-700 p-8 rounded-[2.5rem] text-white shadow-xl shadow-green-100 hover:translate-y-[-4px] transition-all">
            <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-white mb-6">
                <i data-lucide="dollar-sign" class="w-6 h-6"></i>
            </div>
            <p class="text-[10px] font-black text-green-100 uppercase tracking-widest mb-1">Lucro Bruto Real</p>
            <p class="text-3xl font-black tracking-tighter italic">MZN {{ number_format($stats->gross_profit ?? 0, 2) }}</p>
            <div class="mt-4 inline-block px-3 py-1 bg-white/20 rounded-full text-[10px] font-bold">
                Margem: {{ $stats->total_revenue > 0 ? number_format(($stats->gross_profit / $stats->total_revenue) * 100, 1) : 0 }}%
            </div>
        </div>
    </div>

    <!-- Tabela de Produtos Lucrativos -->
    <div class="bg-white rounded-[2.5rem] border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-8 py-6 border-b border-gray-50">
        <h3 class="text-xs font-black text-gray-700 uppercase tracking-widest italic">Detalhamento por Produto</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-[9px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">
                <tr>
                    <th class="px-8 py-4">Produto</th>
                    <th class="px-8 py-4 text-center">Qtd</th>
                    <th class="px-8 py-4 text-right">Receita (Venda)</th>
                    <th class="px-8 py-4 text-right">Custo (Compra)</th>
                    <th class="px-8 py-4 text-right">Lucro Líquido</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($soldProducts as $p)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-8 py-5">
                        <span class="font-bold text-gray-800 uppercase text-xs">{{ $p->name }}</span>
                    </td>
                    <td class="px-8 py-5 text-center">
                        <span class="text-xs font-bold text-gray-500">{{ $p->qty }}</span>
                    </td>
                    <td class="px-8 py-5 text-right text-xs font-medium text-gray-600">
                        {{ number_format($p->item_revenue, 2) }}
                    </td>
                    <td class="px-8 py-5 text-right text-xs font-medium text-orange-600/70">
                        {{ number_format($p->item_cost, 2) }}
                    </td>
                    <td class="px-8 py-5 text-right font-black {{ $p->item_profit > 0 ? 'text-green-600' : 'text-red-600' }}">
                        MZN {{ number_format($p->item_profit, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-900 text-white font-black">
                <tr>
                    <td class="px-8 py-4 text-[10px] uppercase">Totais do Período</td>
                    <td class="px-8 py-4"></td>
                    <td class="px-8 py-4 text-right text-xs">{{ number_format($stats->total_revenue, 2) }}</td>
                    <td class="px-8 py-4 text-right text-xs text-orange-300">{{ number_format($stats->total_cost, 2) }}</td>
                    <td class="px-8 py-4 text-right text-lg italic">MZN {{ number_format($stats->gross_profit, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
</div>

@push('js')
<script>
    function setPeriod(start, end) {
        document.getElementById('start_date').value = start;
        document.getElementById('end_date').value = end;
        document.getElementById('filterForm').submit();
    }
</script>
@endpush
@endsection