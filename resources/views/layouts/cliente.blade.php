<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Agendar Atendimento') - {{ \App\Models\Setting::getCompanyName() }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <?php
    $clientPrimaryColor = \App\Models\Setting::get('client.primary_color', '#3b82f6');
    $clientModalTheme = \App\Models\Setting::get('client.modal_theme', 'light');
    ?>
    
    <style>
        :root {
            /* Client theme colors */
            --client-primary: {{ $clientPrimaryColor }};
            --client-modal-bg: {{ $clientModalTheme === 'dark' ? '#1f2937' : '#ffffff' }};
            --client-modal-text: {{ $clientModalTheme === 'dark' ? '#ffffff' : '#1f2937' }};
            
            /* map legacy vm-gold palette to dynamic brand secondary color */
            --vm-gold: var(--brand-secondary, #D4AF37);
            --vm-gold-50: var(--brand-secondary, #FBF8EC);
            --vm-gold-100: var(--brand-secondary, #F7F1D9);
            --vm-gold-200: var(--brand-secondary, #EFE3B3);
            --vm-gold-300: var(--brand-secondary, #E7D58D);
            --vm-gold-400: var(--brand-secondary, #DFC267);
            --vm-gold-500: var(--brand-secondary, #D4AF37);
            --vm-gold-600: var(--brand-secondary, #B89627);
            --vm-gold-700: var(--brand-secondary, #8B711D);
            --vm-gold-800: var(--brand-secondary, #5E4C13);
            --vm-gold-900: var(--brand-secondary, #312709);
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
        
        /* Client-specific button styles */
        .btn-client-primary {
            background-color: var(--client-primary) !important;
            color: white !important;
            border: none !important;
        }
        
        .btn-client-primary:hover {
            opacity: 0.9 !important;
        }
        
        /* Modal theme */
        .modal-content {
            background-color: var(--client-modal-bg) !important;
            color: var(--client-modal-text) !important;
        }
        
        .modal .bg-white {
            background-color: var(--client-modal-bg) !important;
        }
        
        .modal .text-gray-900 {
            color: var(--client-modal-text) !important;
        }
        
        .modal .text-gray-600 {
            color: {{ $clientModalTheme === 'dark' ? '#9ca3af' : '#4b5563' }} !important;
        }
        
        .modal .border-gray-200 {
            border-color: {{ $clientModalTheme === 'dark' ? '#374151' : '#e5e7eb' }} !important;
        }
        
        .modal .bg-gray-50 {
            background-color: {{ $clientModalTheme === 'dark' ? '#374151' : '#f9fafb' }} !important;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col">
        <!-- Header Simples para Clientes -->
        <header class="bg-gradient-to-r from-vm-gold to-vm-gold-600 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <!-- Logo/Nome à Esquerda -->
                    <div class="flex items-center gap-3">
                        @if(\App\Models\Setting::get('site.logo'))
                            <img src="{{ asset('storage/' . \App\Models\Setting::get('site.logo')) }}" alt="Logo" class="h-10 w-auto" />
                        @else
                            <h1 class="text-2xl font-bold text-white">{{ \App\Models\Setting::getCompanyName() }}</h1>
                        @endif
                    </div>
                    
                    <!-- Botões de Ação -->
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
                            {{-- Lógica do Avatar e Nível --}}
                        @php
                            $currentUser = auth()->user();
                            
                            // Verificar se é um cliente e buscar dados da tabela clientes
                            if ($currentUser) {
                                $cliente = \App\Models\Cliente::where('user_id', $currentUser->id)->first();
                                $avatarFromCliente = $cliente ? $cliente->avatar : null;
                                
                                // Calcular nível baseado em gastos totais
                                if ($cliente) {
                                    $totalGasto = $cliente->agendamentos()
                                        ->whereHas('pagamentos')
                                        ->with('pagamentos')
                                        ->get()
                                        ->sum(function($agendamento) {
                                            // Usar valor_liquido se existir, senão usar valor
                                            return $agendamento->pagamentos->sum('valor_liquido') ?: 
                                                   $agendamento->pagamentos->sum('valor');
                                        });
                                    $nivel = min(100, max(1, floor($totalGasto / 1000))); // Cada R$1000 = 1 nível
                                    $progressoNivel = ($nivel / 100) * 100;
                                } else {
                                    $nivel = 1;
                                    $progressoNivel = 0;
                                }
                            } else {
                                $avatarFromCliente = null;
                                $nivel = 1;
                                $progressoNivel = 0;
                            }
                            
                            $avatarToUse = !empty($avatarFromCliente) ? $avatarFromCliente : ($currentUser ? $currentUser->avatar : null);
                            $hasAvatar = !empty($avatarFromCliente) || ($currentUser && !empty($currentUser->avatar));
                            $avatarPath = $hasAvatar ? asset('storage/' . $avatarToUse) : null;
                        @endphp
                        
                        @if($hasAvatar)
                            <div class="flex items-center gap-3">
                                <!-- Avatar -->
                                <div class="relative">
                                    <img src="{{ $avatarPath }}" 
                                         alt="Avatar" 
                                         class="w-12 h-12 rounded-full object-cover border-2 border-white/30 shadow-lg"
                                         title="{{ $currentUser ? $currentUser->name : 'Visitante' }}"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white"></div>
                                </div>
                                
                                <!-- Sistema de Nível -->
                                <div class="flex flex-col items-center">
                                    <!-- Nível -->
                                    <div class="bg-gradient-to-r from-yellow-400 to-yellow-600 text-white text-xs font-bold px-2 py-1 rounded-full shadow-md">
                                        Nível {{ $nivel }}
                                    </div>
                                    
                                    <!-- Barra de Progresso -->
                                    <div class="w-16 h-2 bg-gray-200 rounded-full overflow-hidden mt-1">
                                        <div class="h-full bg-gradient-to-r from-green-400 to-green-600 rounded-full transition-all duration-500" 
                                             style="width: {{ $progressoNivel }}%">
                                        </div>
                                    </div>
                                    
                                    <!-- Próximo Nível -->
                                    @if($nivel < 100)
                                        <div class="text-xs text-gray-300 mt-1">
                                            R${{ (100 - $nivel) * 10 }} para Nível {{ $nivel + 1 }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Fallback para nome se não tiver avatar -->
                            <span class="text-white text-sm hidden" style="display: none;">{{ $currentUser ? $currentUser->name : 'Visitante' }}</span>
                        @else
                            <span class="text-white text-sm">{{ $currentUser ? $currentUser->name : 'Visitante' }}</span>
                        @endif
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white font-semibold rounded-lg transition-colors">
                                    Sair
                                </button>
                            </form>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- Banner (se existir) -->
        @yield('banner')

        <!-- Conteúdo Principal -->
        <main class="flex-1">
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
                <p class="text-sm">&copy; {{ date('Y') }} {{ \App\Models\Setting::getCompanyName() }}. Todos os direitos reservados.</p>
            </div>
        </footer>
    </div>
</body>
</html>

