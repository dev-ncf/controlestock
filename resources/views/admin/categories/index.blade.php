@extends('layouts.app')

@section('title', 'Categorias de Produtos')

@section('content')
<div class="max-w-5xl mx-auto animate-in fade-in duration-500" x-data="{ openCreate: false, openEdit: false, editId: null, editName: '' }">
    
    <!-- Header -->
    <header class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Categorias</h1>
            <p class="text-sm text-gray-500 font-medium">Organize os seus produtos por grupos.</p>
        </div>
        <button @click="openCreate = true" class="bg-gray-900 text-white px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-black transition-all shadow-lg flex items-center gap-2">
            <i data-lucide="plus-circle" class="w-4 h-4"></i> Nova Categoria
        </button>
    </header>

    <!-- Tabela de Categorias -->
    <div class="bg-white rounded-[2rem] border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                <tr>
                    <th class="px-8 py-4">Nome da Categoria</th>
                    <th class="px-8 py-4 text-center">Total de Produtos</th>
                    <th class="px-8 py-4 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($categories as $cat)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-8 py-5 font-bold text-gray-800 text-sm italic uppercase tracking-tight">
                        {{ $cat->name }}
                    </td>
                    <td class="px-8 py-5 text-center">
                        <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-black">
                            {{ $cat->produtos_count }} itens
                        </span>
                    </td>
                    <td class="px-8 py-5 text-right space-x-2">
                        <!-- Botão Editar -->
                        <button @click="openEdit = true; editId = {{ $cat->id }}; editName = '{{ $cat->name }}'" 
                                class="p-2 text-gray-400 hover:text-blue-600 transition-colors">
                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                        </button>

                        <!-- Botão Apagar -->
                        <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Tem a certeza?')" 
                                    class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- MODAL: CRIAR CATEGORIA -->
    <div x-show="openCreate" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak>
        <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl p-8" @click.away="openCreate = false">
            <h3 class="text-xl font-black text-gray-800 uppercase italic mb-6">Nova Categoria</h3>
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="text-[10px] font-black text-gray-400 uppercase block mb-2">Nome da Categoria</label>
                    <input type="text" name="name" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-100" required placeholder="Ex: Bebidas, Limpeza...">
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="openCreate = false" class="flex-1 py-4 text-xs font-bold text-gray-400 uppercase">Cancelar</button>
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-4 rounded-2xl font-black text-xs uppercase shadow-lg shadow-blue-100">Gravar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: EDITAR CATEGORIA -->
    <div x-show="openEdit" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-cloak>
        <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl p-8" @click.away="openEdit = false">
            <h3 class="text-xl font-black text-gray-800 uppercase italic mb-6">Editar Categoria</h3>
            <form :action="'{{ url('/categorias') }}/' + editId" method="POST">
                @csrf @method('PUT')
                <div class="mb-6">
                    <label class="text-[10px] font-black text-gray-400 uppercase block mb-2">Nome da Categoria</label>
                    <input type="text" name="name" x-model="editName" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-100" required>
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="openEdit = false" class="flex-1 py-4 text-xs font-bold text-gray-400 uppercase">Cancelar</button>
                    <button type="submit" class="flex-1 bg-gray-900 text-white py-4 rounded-2xl font-black text-xs uppercase shadow-lg">Atualizar</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection