<!DOCTYPE html>
<html lang="pt-MZ">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HASSAN COMERCIAL - {{ config('app.name') }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>

    <!-- Alpine.js & Lucide Icons -->
     <link rel="shortcut icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">
     <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <script defer src="{{ asset('js/alpine.js') }}"></script>
    <script src="{{ asset('js/lucide.js') }}"></script>
    <link rel="preload" as="style" href="{{ asset('build/assets/app-BHEasQAD.css') }}">
    <link rel="modulepreload" as="script" href="{{ asset('build/assets/app-CIomGrQN.js') }}">
    <link rel="stylesheet" href="{{ asset('build/assets/app-BHEasQAD.css') }}" data-navigate-track="reload">
    <script type="module" src="{{ asset('build/assets/app-CIomGrQN.js') }}" data-navigate-track="reload"></script>
    @livewireStyles
</head>
<body class="bg-gray-50 antialiased">

   <div class="flex h-screen overflow-hidden"
     x-data="{ sidebarOpen: window.innerWidth >= 1024 }">
        
        <!-- Sidebar Profissional -->
        <aside 
            :class="sidebarOpen ? 'w-64' : 'w-20'" 
            class=" text-white transition-all duration-300 flex flex-col shadow-2xl z-30">
            
            <!-- Logo Area -->
            <div class="p-6 h-16 flex items-center justify-between border-b border-slate-800">
                <div class="flex items-center gap-3" x-show="sidebarOpen">
                    <div class="w-14 h-14 bg-blue-600 rounded-lg flex items-center justify-center">
                        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="w-14 h-14 rounded-xl">
                    </div>
                    <span class="font-black text-xl tracking-tighter"><span class="text-blue-500">HComercial</span></span>
                </div>
                <img src="{{ asset('img/logo.png') }}" alt="Logo" class="w-14 h-14 rounded-xl" x-show="!sidebarOpen" >
            </div>

            <!-- Navegação -->
            <nav class="flex-1 mt-4 px-3 space-y-1 overflow-y-auto custom-scrollbar">
                
                <p x-show="sidebarOpen" class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Principal</p>

                <!-- Item: Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Visão Geral</span>
                </a>
                <!-- Item: Faturação -->
                <a href="{{ route('invoices.create') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs(['invoices.create', 'invoices.show']) ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Vendas</span>
                </a>
                <!-- Item: Prateleira (Produtos Filhos) -->
                <a href="{{ route('products.shelf') }}" 
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('products.shelf') ? 'bg-blue-50 text-blue-600 shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
                    <i data-lucide="layout-grid" class="w-5 h-5 {{ request()->routeIs('products.shelf') ? 'text-blue-600' : 'text-gray-400' }}"></i>
                    <span class="text-sm font-medium">Prateleira (Avulsos)</span>
                    <span class="ml-auto text-[9px] bg-orange-100 text-orange-600 px-2 py-0.5 rounded-full font-black uppercase italic">Frente</span>
                </a>
                <!-- Item: Faturação -->
                <a href="{{ route('invoices.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('invoices.index') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Faturas</span>
                </a>
                 <!-- Item: Receber Dívidas -->
                <a href="{{ route('receipts.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('receipts.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="wallet" class="w-5 h-5"></i>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Receber Dívidas</span>
                </a>
                <!-- Item: Clientes -->
                <a href="{{ route('customers.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('customers.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="users" class="w-5 h-5"></i>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Clientes</span>
                </a>
                @if (auth()->user()->role_id<= 2)
                    
                
                <!-- Item: Estoque -->
                <a href="{{ route('categories.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('categories.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="package" class="w-5 h-5"></i>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Categorias</span>
                </a>
                <!-- Item: Estoque -->
                <a href="{{ route('products.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('products.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="package" class="w-5 h-5"></i>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Stock / Produtos</span>
                </a>

                <!-- Item: Entrada de Mercadoria -->
                <a href="{{ route('stock.entries.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('stock.entries.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="truck" class="w-5 h-5"></i>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Entrada de Stock</span>
                </a>

                <p x-show="sidebarOpen" class="px-4 pt-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Vendas & Finanças</p>

                
                
                <!-- Item: Clientes -->
                <a href="{{ route('suppliers.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('suppliers.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="handshake" class="w-5 h-5"></i>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Fornecedores</span>
                </a>
                

               

                <p x-show="sidebarOpen" class="px-4 pt-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Análise</p>

                <!-- Item: Histórico -->
                <a href="{{ route('stock.movements.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('stock.movements.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="history" class="w-5 h-5"></i>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Movimentações</span>
                </a>

                <!-- Item: Relatório Lucro -->
                <a href="{{ route('reports.profit') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('reports.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 text-yellow-500"></i>
                    <span x-show="sidebarOpen" class="text-sm font-medium">Relatório Lucro</span>
                </a>
                @endif
                @if (auth()->user()->role_id == 1)
                 <!-- Item: Clientes -->
                        <a href="{{ route('users.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <i data-lucide="users" class="w-5 h-5"></i>
                            <span x-show="sidebarOpen" class="text-sm font-medium">Usuarios</span>
                        </a>
                @endif
            </nav>
            <!-- Botão de Recolher Sidebar -->
            <div class="p-4 border-t border-slate-800">
                <button @click="sidebarOpen = !sidebarOpen" class="w-full bg-slate-800 p-3 rounded-xl hover:bg-slate-700 transition-colors flex items-center justify-center">
                    <i data-lucide="chevron-left" x-show="sidebarOpen" class="w-5 h-5"></i>
                    <i data-lucide="chevron-right" x-show="!sidebarOpen" class="w-5 h-5"></i>
                </button>
            </div>
        </aside>

        <!-- Conteúdo Principal -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Header Clean -->
            <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-8 z-20">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-lg bg-gray-100 text-gray-600">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                    <h2 class="text-sm font-bold text-gray-500 uppercase tracking-widest">
                        @yield('title', 'Painel de Controle')
                    </h2>
                </div>
                
                <div class="flex items-center gap-6">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-bold text-gray-800">{{ auth()->user()->name ?? 'Administrador' }}</p>
                        <p class="text-[10px] text-blue-600 font-bold uppercase">Acesso Total</p>
                    </div>
                    <div class="w-10 h-10 bg-slate-100 border border-gray-200 rounded-xl flex items-center justify-center text-slate-600 shadow-sm">
                        <i data-lucide="user" class="w-5 h-5"></i>
                    </div>
                    <form action="{{ route('logout')}}" method="POST" class="border-l pl-6">
                        @csrf
                        <button class="text-gray-400 hover:text-red-500 transition-colors p-2">
                            <i data-lucide="log-out" class="w-5 h-5"></i>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Área de Conteúdo Fluida -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6 md:p-10">
                 {{ $slot ?? '' }}
                 @yield('content')
            </main>
        </div>
    </div>

    <!-- Mensagens de Feedback (Success/Error) -->
    @if ($errors->any())
        @include('error')
    @endif
    @if (session('success'))
        @include('success')
    @endif

    @livewireScripts 
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
     @stack('js') {{-- ESTA LINHA É OBRIGATÓRIA PARA O PUSH FUNCIONAR --}}
</body>
</html>