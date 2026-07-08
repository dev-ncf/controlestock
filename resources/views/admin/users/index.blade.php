@extends('layouts.app')

@section('title', 'Gestão de Utilizadores')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    
    <header class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Utilizadores do Sistema</h1>
            <p class="text-sm text-gray-500 font-medium">Controle quem tem acesso à plataforma e os seus níveis.</p>
        </div>
        <a href="{{ route('users.create') }}" class="bg-gray-900 text-white px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-black transition-all shadow-lg flex items-center gap-2">
            <i data-lucide="user-plus" class="w-4 h-4"></i> Novo Utilizador
        </a>
    </header>

    <div class="bg-white rounded-[2rem] border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                <tr>
                    <th class="px-8 py-4">Utilizador</th>
                    <th class="px-8 py-4">E-mail</th>
                    <th class="px-8 py-4 text-center">Nível de Acesso</th>
                    <th class="px-8 py-4 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xs uppercase">
                                {{ $user->name[0] }}
                            </div>
                            <span class="font-bold text-gray-800">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5 text-gray-500 font-medium">{{ $user->email }}</td>
                    <td class="px-8 py-5 text-center">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase {{ $user->role_id == 1 ? 'bg-red-50 text-red-600' : 'bg-blue-50 text-blue-600' }}">
                            {{ $user->role->label }}
                        </span>
                    </td>
                    <td class="px-8 py-5 text-right">
                        <button class="p-2 text-gray-400 hover:text-blue-600"><i data-lucide="edit-3" class="w-4 h-4"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection