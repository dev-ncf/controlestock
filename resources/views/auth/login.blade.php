<!DOCTYPE html>
<html lang="pt-MZ">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hassan-Comercial</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
      
    <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">
    <link rel="shortcut icon" href="{{ asset('img/logo.png') }}" type="image/png">
    
    <script defer src="{{ asset('js/alpine.js') }}"></script>
    <script src="{{ asset('js/lucide.js') }}"></script>
    <link rel="preload" as="style" href="{{ asset('build/assets/app-BHEasQAD.css') }}">
    <link rel="modulepreload" as="script" href="{{ asset('build/assets/app-CIomGrQN.js') }}">
    <link rel="stylesheet" href="{{ asset('build/assets/app-BHEasQAD.css') }}" data-navigate-track="reload">
    <script type="module" src="{{ asset('build/assets/app-CIomGrQN.js') }}" data-navigate-track="reload"></script>
    @livewireStyles
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-[440px] animate-in fade-in zoom-in duration-500">
        
        <!-- Logo e Branding -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-[1.5rem] shadow-xl shadow-blue-200 mb-4">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" class="w-14 h-14 rounded-xl">
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Bem-vindo de volta</h1>
            <p class="text-slate-500 text-sm font-medium mt-1">Introduza os seus dados para aceder ao sistema</p>
        </div>

        <!-- Card de Login -->
        <div class="bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-2xl shadow-slate-200/50">
            
            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Email -->
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Endereço de E-mail</label>
                    <div class="relative">
                        <i data-lucide="mail" class="absolute left-4 top-3.5 w-4 h-4 text-slate-300"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 text-sm font-bold text-slate-700 placeholder-slate-300 transition-all"
                            placeholder="exemplo@empresa.com">
                    </div>
                    @error('email') <p class="text-red-500 text-[10px] mt-2 font-bold uppercase">{{ $message }}</p> @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Palavra-passe</label>
                    <div class="relative">
                        <i data-lucide="lock" class="absolute left-4 top-3.5 w-4 h-4 text-slate-300"></i>
                        <input type="password" name="password" required
                            class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 text-sm font-bold text-slate-700 placeholder-slate-300 transition-all"
                            placeholder="••••••••">
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-xs font-bold text-slate-500 group-hover:text-slate-700 transition-colors">Lembrar-me</span>
                    </label>
                    <a href="#" class="text-xs font-black text-blue-600 hover:text-blue-700 transition-colors italic">Esqueceu a senha?</a>
                </div>

                <!-- Botão Entrar -->
                <button type="submit" 
                    class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-slate-200 hover:bg-black hover:-translate-y-0.5 active:scale-95 transition-all">
                    Entrar na Plataforma
                </button>
            </form>
        </div>

        <!-- Rodapé do Login -->
        <p class="text-center mt-8 text-slate-400 text-[10px] font-bold uppercase tracking-widest">
            &copy; {{ date('Y') }} MY-ERP Software • Sistema de Gestão
        </p>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>