@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Botão Voltar -->
    <div class="mb-4">
        <a href="{{ url()->previous() }}" class="text-gray-500 hover:text-gray-700 flex items-center text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Voltar para detalhes
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <!-- Cabeçalho com Gradiente -->
        <div class="bg-gradient-to-r from-gray-800 to-gray-900 px-8 py-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold">Registar Pagamento</h2>
                    <p class="text-gray-400 text-sm">Fatura #{{ $invoice->invoice_number }}</p>
                </div>
                <div class="bg-white/10 px-4 py-2 rounded-lg backdrop-blur-sm text-right">
                    <span class="block text-xs uppercase text-gray-400">Cliente</span>
                    <span class="font-bold text-white">{{ $invoice->cliente->name }}</span>
                </div>
            </div>
        </div>

        <div class="p-8">
            <!-- Resumo Financeiro -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="flex items-center p-4 bg-blue-50 rounded-xl border border-blue-100">
                    <div class="bg-blue-500 p-3 rounded-lg text-white mr-4">
                        <i class="fas fa-file-invoice-dollar fa-lg"></i>
                    </div>
                    <div>
                        <span class="block text-xs text-blue-600 uppercase font-bold">Total da Fatura</span>
                        <p class="text-xl font-black text-blue-900">MZN {{ number_format($invoice->total_amount, 2) }}</p>
                    </div>
                </div>

                <div class="flex items-center p-4 bg-orange-50 rounded-xl border border-orange-100">
                    <div class="bg-orange-500 p-3 rounded-lg text-white mr-4">
                        <i class="fas fa-clock fa-lg"></i>
                    </div>
                    <div>
                        <span class="block text-xs text-orange-600 uppercase font-bold">Saldo em Aberto</span>
                        <p class="text-xl font-black text-orange-900" id="current_balance_val">MZN {{ number_format($remaining, 2) }}</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('receipts.store') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="venda_id" value="{{ $invoice->id }}">

                <!-- Campo de Valor de Pagamento -->
                <div class="bg-gray-50 p-6 rounded-2xl border-2 border-dashed border-gray-200">
                    <label class="block text-center text-sm font-bold text-gray-600 uppercase mb-3">Valor que está a ser pago agora</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-2xl font-bold text-gray-400">MZN</span>
                        <input type="number" 
                               name="amount_paid" 
                               id="amount_paid"
                               step="0.01" 
                               max="{{ $remaining }}" 
                               value="{{ $remaining }}" 
                               class="w-full pl-20 pr-4 py-4 border-none bg-white rounded-xl text-3xl font-black text-green-600 focus:ring-2 focus:ring-green-500 shadow-sm" 
                               required>
                    </div>
                    <!-- Preview de Saldo Remanescente -->
                    <div class="mt-4 flex justify-between items-center text-sm px-2">
                        <span class="text-gray-500">Saldo após este pagamento:</span>
                        <span class="font-bold text-gray-700" id="new_balance_preview">MZN 0,00</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-wallet mr-1 text-gray-400"></i> Método de Pagamento
                        </label>
                        <select name="payment_method" class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="Numerário">💸 Numerário (Dinheiro)</option>
                            <option value="TPA">💳 TPA (Multicaixa)</option>
                            <option value="Transferência">🏦 Transferência Bancária</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-tag mr-1 text-gray-400"></i> Referência / Comprovativo
                        </label>
                        <input type="text" name="reference" placeholder="Nº de borderô ou transação" 
                               class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 flex flex-col md:flex-row gap-4">
                    <button type="submit" class="flex-[2] bg-green-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-green-700 shadow-lg shadow-green-200 transition-all transform hover:-translate-y-1 flex items-center justify-center">
                        <i class="fas fa-check-circle mr-2"></i> CONFIRMAR RECEBIMENTO
                    </button>
                    <a href="{{ route('receipts.index') }}" class="flex-1 bg-gray-100 text-center py-4 rounded-xl font-bold text-gray-600 hover:bg-gray-200 transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Lógica para atualizar o saldo remanescente em tempo real
    const amountInput = document.getElementById('amount_paid');
    const newBalancePreview = document.getElementById('new_balance_preview');
    const remainingTotal = {{ $remaining }};

    function updatePreview() {
        const value = parseFloat(amountInput.value) || 0;
        const diff = remainingTotal - value;
        
        newBalancePreview.innerText = 'MZN ' + diff.toLocaleString('pt-MZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        
        if (diff < 0) {
            newBalancePreview.classList.add('text-red-600');
        } else {
            newBalancePreview.classList.remove('text-red-600');
        }
    }

    amountInput.addEventListener('input', updatePreview);
    window.onload = updatePreview;
</script>
@endsection