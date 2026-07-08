@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Histórico de Entradas / Compras</h2>
            <p class="text-sm text-gray-500">Acompanhe a reposição do seu stock e as compras aos fornecedores.</p>
        </div>
        <a href="{{ route('stock.entries.create') }}" class="bg-slate-800 text-white px-4 py-2 rounded-lg hover:bg-slate-900 transition font-bold text-sm">
            <i class="fas fa-plus mr-2"></i> Nova Entrada de Stock
        </a>
    </div>

    <!-- Alertas de Sucesso/Erro -->
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabela de Movimentos -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Data/Hora</th>
                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Produto</th>
                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase text-center">Quantidade</th>
                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Motivo / Fornecedor</th>
                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Utilizador</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($movements as $m)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-4 text-sm text-gray-600">
                        {{ $m->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-4 py-4">
                        <div class="font-bold text-gray-800">{{ $m->produto->name }}</div>
                        <div class="text-xs text-gray-400">SKU: {{ $m->produto->sku }}</div>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-black">
                            + {{ $m->quantity }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-600 italic">
                        {{ $m->reason }}
                    </td>
                    <td class="px-4 py-4 text-sm">
                        <span class="text-gray-700 font-medium">{{ $m->user->name ?? 'Sistema' }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-10 text-center text-gray-400 italic">
                        Nenhuma entrada de mercadoria registada até ao momento.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="mt-4">
        {{ $movements->links() }}
    </div>
</div>
@endsection