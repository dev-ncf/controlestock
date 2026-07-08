@extends('layouts.app')

@section('content')
<div class="grid grid-cols-12 gap-6">
    
    <!-- Esquerda: Formulário de Adição -->
    <div class="col-span-12 lg:col-span-8">
        <div class="bg-white p-6 rounded-xl shadow-sm mb-6">
            <h3 class="font-bold mb-4">1. Selecionar Produtos para Entrada</h3>
            <form action="{{ route('stock.entries.addItem') }}" method="POST" class="grid grid-cols-12 gap-4">
                @csrf
                <div class="col-span-6">
                    <label class="text-xs font-bold text-gray-500 uppercase">Produto</label>
                    <select name="product_id" class="w-full border-gray-300 rounded-lg">
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} (Atual: {{ $p->stock_quantity }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="text-xs font-bold text-gray-500 uppercase">Qtd</label>
                    <input type="number" name="quantity" value="1" class="w-full border-gray-300 rounded-lg">
                </div>
                <div class="col-span-3">
                    <label class="text-xs font-bold text-gray-500 uppercase">Preço Custo (Unit)</label>
                    <input type="number" step="0.01" name="purchase_price" placeholder="MZN" class="w-full border-gray-300 rounded-lg" required>
                </div>
                <div class="col-span-1 flex items-end">
                    <button type="submit" class="bg-blue-600 text-white p-3 rounded-lg w-full"><i class="fas fa-plus"></i></button>
                </div>
            </form>
        </div>

        <!-- Tabela de Itens Temporária -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-4">Produto</th>
                        <th class="p-4 text-center">Qtd</th>
                        <th class="p-4 text-right">Custo Unit.</th>
                        <th class="p-4 text-right">Subtotal</th>
                        <th class="p-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($cart as $index => $item)
                    <tr>
                        <td class="p-4">{{ $item['name'] }}</td>
                        <td class="p-4 text-center font-bold text-blue-600">+ {{ $item['quantity'] }}</td>
                        <td class="p-4 text-right">MZN {{ number_format($item['purchase_price'], 2) }}</td>
                        <td class="p-4 text-right font-bold">MZN {{ number_format($item['subtotal'], 2) }}</td>
                        <td class="p-4 text-center">
                            <a href="{{ route('stock.entries.removeItem', $index) }}" class="text-red-500"><i class="fas fa-times"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Direita: Fornecedor e Finalização -->
    <div class="col-span-12 lg:col-span-4">
        <form action="{{ route('stock.entries.store') }}" method="POST" class="bg-white p-6 rounded-xl shadow-sm space-y-4">
            @csrf
            <h3 class="font-bold border-b pb-2 text-gray-700 uppercase text-sm">2. Origem e Pagamento</h3>
            
            <div>
                <label class="text-xs font-bold text-gray-500 uppercase">Fornecedor</label>
                <select name="supplier_id" class="w-full border-gray-300 rounded-lg" required>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-gray-500 uppercase">Pagamento ao Fornecedor</label>
                <select name="payment_status" class="w-full border-gray-300 rounded-lg">
                    <option value="paid">Pago (Saída de Caixa)</option>
                    <option value="unpaid">A Prazo (Gera Dívida com Fornecedor)</option>
                </select>
            </div>

            <div class="pt-4 border-t">
                <p class="text-gray-500 text-xs font-bold uppercase">Total da Compra:</p>
                <p class="text-3xl font-black text-blue-700">MZN {{ number_format($total, 2) }}</p>
            </div>

            <button type="submit" class="w-full bg-slate-800 text-white py-4 rounded-xl font-bold hover:bg-slate-900 transition shadow-lg">
                <i class="fas fa-truck-loading mr-2"></i> FINALIZAR ENTRADA
            </button>
        </form>
    </div>
</div>
@endsection