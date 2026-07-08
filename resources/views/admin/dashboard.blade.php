@extends('layouts.app')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    
    <!-- 1. CABEÇALHO -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-800 uppercase tracking-wider italic">
                {{ auth()->user()->role_id <= 2 ? 'Painel de Gestão' : 'Terminal de Operador' }}
            </h1>
            <p class="text-sm text-gray-500 font-medium">
                {{ auth()->user()->role_id <= 2 ? 'Resumo da lucratividade e saúde financeira.' : 'Acompanhe o seu desempenho hoje.' }}
            </p>
        </div>

        <a href="{{ route('invoices.create') }}" 
           class="flex items-center justify-center gap-3 bg-blue-600 text-white px-8 py-4 rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl shadow-blue-200 hover:bg-blue-700 hover:-translate-y-1 transition-all active:scale-95">
            <i data-lucide="shopping-cart" class="w-5 h-5"></i>
            Nova Venda
        </a>
    </div>

    <!-- 2. GRID DE INDICADORES DE LUCRO (SÓ ADMIN/GERENTE) -->
    @if(auth()->user()->role_id <= 2)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Hoje -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-2 h-full bg-sky-400"></div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Lucro Hoje</p>
            <p class="text-2xl font-black text-slate-800 tracking-tighter">MZN {{ number_format($dailyProfit->gross_profit ?? 0, 2) }}</p>
            <div class="mt-4 flex items-center justify-between">
                <span class="text-[9px] font-bold text-gray-400 uppercase italic">Bruto: {{ number_format($dailyProfit->total_revenue ?? 0, 0) }}</span>
                <i data-lucide="calendar" class="w-4 h-4 text-sky-400"></i>
            </div>
        </div>

        <!-- Semanal -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-2 h-full bg-indigo-500"></div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Lucro Semanal</p>
            <p class="text-2xl font-black text-slate-800 tracking-tighter">MZN {{ number_format($weeklyProfit->gross_profit ?? 0, 2) }}</p>
            <div class="mt-4 flex items-center justify-between">
                <span class="text-[9px] font-bold text-gray-400 uppercase italic">Bruto: {{ number_format($weeklyProfit->total_revenue ?? 0, 0) }}</span>
                <i data-lucide="bar-chart-3" class="w-4 h-4 text-indigo-500"></i>
            </div>
        </div>

        <!-- Mensal -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-2 h-full bg-emerald-500"></div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Lucro Mensal</p>
            <p class="text-2xl font-black text-slate-800 tracking-tighter">MZN {{ number_format($monthlyProfit->gross_profit ?? 0, 2) }}</p>
            <div class="mt-4 flex items-center justify-between">
                <span class="text-[9px] font-bold text-gray-400 uppercase italic">Bruto: {{ number_format($monthlyProfit->total_revenue ?? 0, 0) }}</span>
                <i data-lucide="pie-chart" class="w-4 h-4 text-emerald-500"></i>
            </div>
        </div>

        <!-- Anual -->
        <div class="bg-slate-900 p-6 rounded-3xl shadow-xl border border-slate-800 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-2 h-full bg-amber-500"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Lucro Anual</p>
            <p class="text-2xl font-black text-white tracking-tighter">MZN {{ number_format($annualProfit->gross_profit ?? 0, 2) }}</p>
            <div class="mt-4 flex items-center justify-between">
                <span class="text-[9px] font-bold text-slate-500 uppercase italic">Acumulado {{ date('Y') }}</span>
                <i data-lucide="trophy" class="w-4 h-4 text-amber-500"></i>
            </div>
        </div>
    </div>

    <!-- 3. GRID SECUNDÁRIO (STOCKS E FINANCEIRO - SÓ ADMIN) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Valor em Stock -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-6">
            <div class="bg-amber-50 p-4 rounded-2xl text-amber-600">
                <i data-lucide="package" class="w-8 h-8"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Valorização de Stock</p>
                <p class="text-xl font-black text-gray-800">MZN {{ number_format($stockData['total_valuation'], 2) }}</p>
            </div>
        </div>

        <!-- A Receber -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-6">
            <div class="bg-red-50 p-4 rounded-2xl text-red-600">
                <i data-lucide="hand-holding-usd" class="w-8 h-8"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Dívidas de Clientes</p>
                <p class="text-xl font-black text-red-600">MZN {{ number_format($financial['total_to_receive'], 2) }}</p>
            </div>
        </div>
    </div>

    @else
    <!-- GRID PARA OPERADOR / VENDEDOR -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-8 rounded-3xl border-b-4 border-blue-500 shadow-sm">
             <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Minhas Vendas (Hoje)</p>
             <p class="text-3xl font-black text-slate-800 tracking-tighter">MZN {{ number_format($dailyProfit->total_revenue ?? 0, 2) }}</p>
        </div>
        <div class="bg-white p-8 rounded-3xl border-b-4 border-orange-500 shadow-sm">
             <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Status do Caixa</p>
             <p class="text-3xl font-black text-orange-600 uppercase italic">Aberto</p>
        </div>
        <div class="bg-white p-8 rounded-3xl border-b-4 border-emerald-500 shadow-sm flex items-center justify-between">
             <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Disponibilidade</p>
                <p class="text-xl font-black text-gray-800 uppercase">Em Stock</p>
             </div>
             <i data-lucide="check-circle" class="w-8 h-8 text-emerald-500"></i>
        </div>
    </div>
    @endif

    <!-- 4. ATALHOS RÁPIDOS -->
    <div class="space-y-4">
        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Acesso Rápido</h3>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('invoices.create') }}" class="flex items-center gap-3 bg-white border border-gray-100 px-6 py-4 rounded-2xl text-xs font-bold text-gray-600 hover:bg-gray-50 transition-all shadow-sm">
                <i data-lucide="shopping-cart" class="w-4 h-4 text-blue-500"></i> Frente de Caixa
            </a>
            @if(auth()->user()->role_id <= 2)
            <a href="{{ route('products.create') }}" class="flex items-center gap-3 bg-white border border-gray-100 px-6 py-4 rounded-2xl text-xs font-bold text-gray-600 hover:bg-gray-50 transition-all shadow-sm">
                <i data-lucide="plus-circle" class="w-4 h-4 text-emerald-500"></i> Novo Produto
            </a>
            <a href="{{ route('reports.profit') }}" class="flex items-center gap-3 bg-white border border-gray-100 px-6 py-4 rounded-2xl text-xs font-bold text-gray-600 hover:bg-gray-50 transition-all shadow-sm">
                <i data-lucide="file-text" class="w-4 h-4 text-indigo-500"></i> Relatórios
            </a>
            @endif
        </div>
    </div>

    <!-- 5. TABELAS DE PERFORMANCE E ALERTAS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Stock Crítico -->
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-red-50/30">
                <h3 class="font-black text-gray-700 uppercase text-[10px] tracking-widest italic">Atenção: Stock Crítico</h3>
                <i data-lucide="alert-triangle" class="w-4 h-4 text-red-500"></i>
            </div>
            <table class="w-full text-left">
                <tbody class="divide-y divide-gray-50">
                    @forelse($stockData['low_stock_products'] as $p)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-xs font-bold text-gray-800 uppercase">{{ $p->name }}</td>
                        <td class="px-6 py-4 text-right">
                            <span class="bg-red-100 text-red-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase">{{ $p->stock_quantity }} un</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="2" class="p-8 text-center text-gray-400 italic text-xs">Sem alertas de stock.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(auth()->user()->role_id <= 2)
            <!-- Top Produtos por Lucro -->
            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-emerald-50/30">
                    <h3 class="font-black text-emerald-800 uppercase text-[10px] tracking-widest italic">Performance de Lucro (Top 5)</h3>
                    <i data-lucide="trending-up" class="w-4 h-4 text-emerald-500"></i>
                </div>
                <table class="w-full text-left">
                    <tbody class="divide-y divide-gray-50">
                        @foreach($topSellers as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-xs font-bold text-gray-800 uppercase">{{ $item->name }}</td>
                            <td class="px-6 py-4 text-right text-emerald-600 font-black text-xs">MZN {{ number_format($item->total_earned, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>
</div>
@endsection