@extends('layouts.app')

@section('title', 'Novo Cliente')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Cabeçalho -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Cadastrar Novo Cliente</h2>
            <p class="text-gray-500 text-sm">Preencha os dados abaixo para registar um novo cliente no sistema.</p>
        </div>
        <a href="{{ route('customers.index') }}" class="text-gray-500 hover:text-gray-700 font-medium">
            <i class="fas fa-arrow-left mr-1"></i> Voltar à Lista
        </a>
    </div>

    <!-- Formulário -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <form action="{{ route('customers.store') }}" method="POST" class="p-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Nome Completo -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Nome Completo / Empresa *</label>
                    <input type="text" name="name" value="{{ old('name') }}" 
                        class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror" 
                        placeholder="Ex: João Lourenço ou Empresa LDA" required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- NIF -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-bold text-gray-700 uppercase mb-2">NIF (Opcional)</label>
                    <input type="text" name="nif" value="{{ old('nif') }}" 
                        class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('nif') border-red-500 @enderror" 
                        placeholder="Número de Identificação Fiscal">
                    @error('nif') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Telemóvel -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Telemóvel / Telefone</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-phone"></i>
                        </span>
                        <input type="text" name="phone" value="{{ old('phone') }}" 
                            class="w-full pl-10 border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                            placeholder="Ex: 923 000 000">
                    </div>
                </div>

                <!-- Limite de Crédito -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Limite de Crédito (KZ) *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <strong>KZ</strong>
                        </span>
                        <input type="number" step="0.01" name="credit_limit" value="{{ old('credit_limit', 0) }}" 
                            class="w-full pl-12 border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('credit_limit') border-red-500 @enderror" 
                            required>
                    </div>
                    <p class="text-gray-400 text-[10px] mt-1 italic">Valor máximo de dívida permitido para este cliente.</p>
                    @error('credit_limit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

            </div>

            <!-- Botões de Ação -->
            <div class="mt-10 pt-6 border-t border-gray-100 flex items-center justify-end space-x-4">
                <button type="reset" class="px-6 py-3 rounded-xl text-gray-500 font-bold hover:bg-gray-50 transition">
                    Limpar Dados
                </button>
                <button type="submit" class="px-10 py-3 bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i> Gravar Cliente
                </button>
            </div>
        </form>
    </div>

    <!-- Dica de Negócio -->
    <div class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-100 flex items-start space-x-3 text-blue-800">
        <i class="fas fa-info-circle mt-1"></i>
        <p class="text-sm">
            <strong>Dica:</strong> Ao definir um <strong>Limite de Crédito</strong>, o sistema poderá alertá-lo caso o cliente tente realizar uma compra a prazo que ultrapasse este valor.
        </p>
    </div>
</div>
@endsection