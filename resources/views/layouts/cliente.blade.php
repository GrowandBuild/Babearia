<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Agendar Atendimento') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --vm-gold: #D4AF37;
            --vm-gold-50: #FBF8EC;
            --vm-gold-100: #F7F1D9;
            --vm-gold-200: #EFE3B3;
            --vm-gold-300: #E7D58D;
            --vm-gold-400: #DFC267;
            --vm-gold-500: #D4AF37;
            --vm-gold-600: #B89627;
            --vm-gold-700: #8B711D;
            --vm-gold-800: #5E4C13;
            --vm-gold-900: #312709;
            --vm-navy-50: #E8EBF5;
            --vm-navy-100: #D1D7EB;
            --vm-navy-200: #A3AFD7;
            --vm-navy-300: #7587C3;
            --vm-navy-400: #475FAF;
            --vm-navy-500: #1A379B;
            --vm-navy-600: #142C7C;
            --vm-navy-700: #0F215D;
            --vm-navy-800: #0A1647;
            --vm-navy-900: #050B24;
        }
        
        .bg-vm-gold { background-color: var(--vm-gold); }
        .bg-vm-gold-50 { background-color: var(--vm-gold-50); }
        .bg-vm-gold-100 { background-color: var(--vm-gold-100); }
        .bg-vm-gold-200 { background-color: var(--vm-gold-200); }
        .bg-vm-gold-300 { background-color: var(--vm-gold-300); }
        .bg-vm-gold-400 { background-color: var(--vm-gold-400); }
        .bg-vm-gold-500 { background-color: var(--vm-gold-500); }
        .bg-vm-gold-600 { background-color: var(--vm-gold-600); }
        .bg-vm-gold-700 { background-color: var(--vm-gold-700); }
        .bg-vm-gold-800 { background-color: var(--vm-gold-800); }
        .bg-vm-gold-900 { background-color: var(--vm-gold-900); }
        
        .text-vm-gold { color: var(--vm-gold); }
        .text-vm-gold-600 { color: var(--vm-gold-600); }
        .text-vm-gold-700 { color: var(--vm-gold-700); }
        .text-vm-gold-800 { color: var(--vm-gold-800); }
        
        .border-vm-gold { border-color: var(--vm-gold); }
        .border-vm-gold-300 { border-color: var(--vm-gold-300); }
        
        .from-vm-gold { --tw-gradient-from: var(--vm-gold); }
        .to-vm-gold-600 { --tw-gradient-to: var(--vm-gold-600); }
        
        .ring-vm-gold { --tw-ring-color: var(--vm-gold); }
    </style>
</head>
<body class="font-sans antialiased bg-gradient-to-br from-gray-100 via-gray-50 to-gray-200">
    <div class="min-h-screen flex flex-col">
        <!-- Header Simples para Clientes -->
        <header class="bg-gradient-to-r from-vm-gold to-vm-gold-600 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-white">VIDA MARIA Esmalteria</h1>
                    </div>
                    <div class="flex items-center gap-4">
                        @auth
                            @can('isProprietaria')
                                <a href="{{ route('agendamentos.agenda') }}" 
                                   class="px-4 py-2 bg-white text-vm-gold font-bold rounded-lg hover:bg-gray-100 transition-colors shadow-md flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                    </svg>
                                    Área Administrativa
                                </a>
                            @endcan
                            <span class="text-white text-sm">{{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white font-semibold rounded-lg transition-colors">
                                    Sair
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white font-semibold rounded-lg transition-colors">
                                Entrar
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- Conteúdo Principal -->
        <main class="flex-1 py-8">
            @if(session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer Simples -->
        <footer class="bg-gray-800 text-white py-6 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-sm">&copy; {{ date('Y') }} VIDA MARIA Esmalteria. Todos os direitos reservados.</p>
            </div>
        </footer>
    </div>
</body>
</html>

