@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Resumo de Dívidas -->
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm flex justify-between items-center">
        <div>
            <h3 class="text-red-800 font-bold uppercase text-sm">Total a Receber (Dívidas Ativas)</h3>
            <p class="text-3xl font-black text-red-600">MZN {{ number_format($totalDebt, 2) }}</p>
        </div>
        <a href="{{ route('customers.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:bg-blue-700">
            <i class="fas fa-user-plus mr-2"></i> Novo Cliente
        </a>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm">
        <!-- Input de Busca Real-time -->
        <div class="flex gap-4 mb-6">
            <input type="text" id="searchInput" 
                placeholder="Pesquisar por nome ou telemóvel em tempo real..." 
                class="flex-1 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
        </div>

        <table class="w-full text-left" id="customersTable">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Nome</th>
                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase text-center">Telemóvel</th>
                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase text-right">Limite de Crédito</th>
                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase text-right">Saldo Devedor</th>
                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase text-center">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y" id="customerTableBody">
                @foreach($customers as $customer)
                <!-- Adicionada a classe 'customer-row' -->
                <tr class="hover:bg-gray-50 customer-row">
                    <td class="px-4 py-4 font-bold text-gray-800 customer-name">{{ $customer->name }}</td>
                    <td class="px-4 py-4 text-center text-gray-600 customer-phone">{{ $customer->phone ?? '---' }}</td>
                    <td class="px-4 py-4 text-right text-gray-600">MZN {{ number_format($customer->credit_limit, 2) }}</td>
                    <td class="px-4 py-4 text-right">
                        <span class="{{ $customer->current_balance > 0 ? 'text-red-600 font-black' : 'text-green-600' }}">
                            MZN {{ number_format($customer->current_balance, 2) }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <a href="{{ route('customers.show', $customer->id) }}" class="bg-gray-100 text-gray-700 px-3 py-1 rounded hover:bg-gray-200">
                            <i class="fas fa-eye"></i> Histórico
                        </a>
                    </td>
                </tr>
                @endforeach
                <!-- Linha para quando não houver resultados -->
                <tr id="noResults" class="hidden">
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">Nenhum cliente encontrado.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Script para busca em tempo real -->
<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('.customer-row');
        let hasResults = false;

        rows.forEach(row => {
            const name = row.querySelector('.customer-name').textContent.toLowerCase();
            const phone = row.querySelector('.customer-phone').textContent.toLowerCase();

            if (name.includes(searchTerm) || phone.includes(searchTerm)) {
                row.style.display = ""; // Mostra a linha
                hasResults = true;
            } else {
                row.style.display = "none"; // Esconde a linha
            }
        });

        // Mostrar mensagem de "Nenhum resultado" se necessário
        const noResultsRow = document.getElementById('noResults');
        if (hasResults) {
            noResultsRow.classList.add('hidden');
        } else {
            noResultsRow.classList.remove('hidden');
        }
    });
</script>
@endsection