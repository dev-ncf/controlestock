@extends('layouts.app')

@section('title', 'Gestão de Fornecedores')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500" 
     x-data="{ 
        openCreate: false, 
        search: '',
        allSuppliers: {{ $suppliers->toJson() }},
        formatMoney(value) {
            return new Intl.NumberFormat('pt-MZ', { minimumFractionDigits: 2 }).format(value);
        },
        get filteredSuppliers() {
            if (this.search.trim() === '') return this.allSuppliers;
            const s = this.search.toLowerCase();
            return this.allSuppliers.filter(sup => 
                sup.name.toLowerCase().includes(s) || 
                (sup.phone && sup.phone.toLowerCase().includes(s)) ||
                (sup.nif && sup.nif.toLowerCase().includes(s))
            );
        }
     }">
    
    <!-- Resumo Financeiro -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="w-full">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total em Dívida (Contas a Pagar)</p>
                <p class="text-4xl font-black text-red-600 tracking-tighter italic">MZN {{ number_format($totalToPay, 2) }}</p>
            </div>
            <button @click="openCreate = true" class=" md:w-auto bg-gray-900 text-white px-6 py-4 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-black transition-all shadow-xl flex items-center justify-center gap-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Novo Fornecedor
            </button>
        </div>
        
        <div class="bg-blue-600 p-8 rounded-[2.5rem] text-white shadow-xl shadow-blue-100 flex flex-col justify-center relative overflow-hidden">
            <p class="text-[10px] font-bold uppercase opacity-80 mb-1">Fornecedores Ativos</p>
            <p class="text-3xl font-black" x-text="allSuppliers.length"></p>
            <i data-lucide="truck" class="absolute -right-4 -bottom-4 w-24 h-24 opacity-10"></i>
        </div>
    </div>

    <!-- BARRA DE PESQUISA -->
    <div class="bg-white p-4 rounded-[2rem] border border-gray-100 shadow-sm">
        <div class="relative">
            <input type="text" x-model="search"
                placeholder="Pesquisar por nome, NIF ou telefone..."
                class="w-full bg-gray-50 border-none rounded-xl p-4 pl-12 text-sm font-bold focus:ring-2 focus:ring-blue-100 transition-all"
            >
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                <i data-lucide="search" class="w-5 h-5"></i>
            </div>
            <div class="absolute right-4 top-1/2 -translate-y-1/2" x-show="search.length > 0">
                <button @click="search = ''" class="text-gray-400 hover:text-red-500">
                    <i data-lucide="x-circle" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Tabela de Fornecedores -->
    <div class="bg-white rounded-[2.5rem] border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                <tr>
                    <th class="px-8 py-4">Fornecedor</th>
                    <th class="px-8 py-4 text-center">Contacto</th>
                    <th class="px-8 py-4 text-right">Saldo a Pagar</th>
                    <th class="px-8 py-4 text-right">Ação</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm">
                <template x-for="s in filteredSuppliers" :key="s.id">
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-800 uppercase" x-text="s.name"></span>
                                <span class="text-[9px] text-gray-400 font-mono" x-text="'NIF: ' + (s.nif || '---')"></span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-center text-gray-500 font-medium" x-text="s.phone || '---'"></td>
                        <td class="px-8 py-5 text-right font-black" 
                            :class="s.balance_to_pay > 0 ? 'text-red-600' : 'text-green-600'">
                            MZN <span x-text="formatMoney(s.balance_to_pay)"></span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <a :href="'{{ route('suppliers.show', ':id') }}'.replace(':id', s.id)" 
                               class="inline-block p-2 bg-gray-100 text-gray-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <!-- Estado Vazio -->
        <div x-show="filteredSuppliers.length === 0" class="p-20 text-center text-gray-400 italic">
            <i data-lucide="user-search" class="w-12 h-12 mx-auto mb-4 opacity-20"></i>
            <p>Nenhum fornecedor encontrado.</p>
        </div>
    </div>

    <!-- MODAL: NOVO FORNECEDOR -->
    <div x-show="openCreate" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" 
         x-cloak 
         x-transition>
        <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl p-10" @click.away="openCreate = false">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-black text-gray-800 uppercase italic">Registar Fornecedor</h3>
                <button @click="openCreate = false" class="text-gray-400 hover:text-red-500">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <form action="{{ route('suppliers.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase block mb-2 tracking-widest">Nome / Empresa</label>
                    <input type="text" name="name" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-100" placeholder="Ex: Cervejas de Moçambique" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase block mb-2 tracking-widest">NIF</label>
                        <input type="text" name="nif" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold" placeholder="400...">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase block mb-2 tracking-widest">Telefone</label>
                        <input type="text" name="phone" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold" placeholder="84...">
                    </div>
                </div>
                <div class="pt-6 flex gap-3">
                    <button type="button" @click="openCreate = false" class="flex-1 py-4 text-xs font-bold text-gray-400 uppercase hover:text-gray-600 transition-colors">Cancelar</button>
                    <button type="submit" class="flex-[2] bg-blue-600 text-white py-4 rounded-2xl font-black text-xs uppercase shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">
                        Gravar Fornecedor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection