@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto animate-in fade-in duration-500">
    
    <header class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Novo Utilizador</h1>
        <p class="text-sm text-gray-500">Defina as credenciais e o papel do novo membro.</p>
    </header>

    <div class="bg-white p-10 rounded-[2.5rem] border border-gray-200 shadow-sm">
        <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nome -->
                <div class="col-span-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Nome Completo</label>
                    <input type="text" name="name" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-100" required>
                </div>

                <!-- Email -->
                <div class="col-span-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">E-mail de Acesso</label>
                    <input type="email" name="email" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-100" required>
                </div>

                <!-- Role (O ponto chave) -->
                <div class="col-span-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Nível de Permissão</label>
                    <select name="role_id" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-100">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Password -->
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Palavra-passe</label>
                    <input type="password" name="password" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-100" required>
                </div>

                <!-- Password Confirm -->
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Confirmar</label>
                    <input type="password" name="password_confirmation" class="w-full bg-gray-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-blue-100" required>
                </div>
            </div>

            <div class="pt-6 flex gap-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all">
                    Criar Conta
                </button>
                <a href="{{ route('users.index') }}" class="flex-1 bg-gray-100 text-gray-500 py-4 rounded-2xl font-black text-xs uppercase tracking-widest text-center">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection