@extends('layouts.app')

@section('title', 'Agenda')

@section('content')
<div class="max-w-full mx-auto sm:px-6 lg:px-8">
    <!-- Header com Navegação e Filtros -->
    <div class="bg-gradient-to-r from-vm-gold to-vm-gold-600 shadow-lg mb-4 sm:mb-6 rounded-lg">
        <div class="p-4 sm:p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Navegação de Data -->
                <div class="flex items-center gap-3 flex-wrap">
                    <a href="{{ route('agendamentos.agenda', ['data' => \Carbon\Carbon::parse($data)->subDay()->format('Y-m-d'), 'profissional_id' => $profissionalId]) }}" 
                       class="p-2 bg-white hover:bg-gray-100 rounded-lg transition-colors shadow-md border-2 border-white/50">
                        <svg class="w-5 h-5 text-vm-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <a href="{{ route('agendamentos.agenda', ['data' => today()->format('Y-m-d'), 'profissional_id' => $profissionalId]) }}" 
                       class="px-4 py-2 bg-white hover:bg-gray-100 rounded-lg text-vm-gold font-bold transition-colors shadow-md border-2 border-white/50">
                        Hoje
                    </a>
                    <a href="{{ route('agendamentos.agenda', ['data' => \Carbon\Carbon::parse($data)->addDay()->format('Y-m-d'), 'profissional_id' => $profissionalId]) }}" 
                       class="p-2 bg-white hover:bg-gray-100 rounded-lg transition-colors shadow-md border-2 border-white/50">
                        <svg class="w-5 h-5 text-vm-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <div class="px-4 py-2 bg-white rounded-lg shadow-md border-2 border-white/50">
                        <input type="date" 
                               value="{{ $data }}" 
                               onchange="window.location.href='{{ route('agendamentos.agenda') }}?data=' + this.value + '&profissional_id={{ $profissionalId }}'"
                               class="border-0 focus:ring-0 text-gray-900 font-bold text-sm">
                    </div>
                    <div class="px-4 py-2 bg-white rounded-lg shadow-md border-2 border-white/50">
                        <span class="text-vm-gold font-bold text-lg">
                            {{ \Carbon\Carbon::parse($data)->locale('pt_BR')->translatedFormat('d M Y - l') }}
                        </span>
                    </div>
                </div>

                <!-- Filtros e Ações -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    @can('isProprietaria')
                    <form method="GET" action="{{ route('agendamentos.agenda') }}" class="flex gap-2">
                        <input type="hidden" name="data" value="{{ $data }}">
                        <select name="profissional_id" 
                                onchange="this.form.submit()"
                                class="rounded-lg border-2 border-white/50 bg-white text-gray-900 font-semibold shadow-md px-3 py-2 focus:ring-2 focus:ring-white focus:border-white">
                            <option value="">Todos os Profissionais</option>
                            @foreach($profissionais as $prof)
                                <option value="{{ $prof->id }}" {{ $profissionalId == $prof->id ? 'selected' : '' }}>
                                    {{ $prof->nome }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                    @endcan
                    
                    <a href="{{ route('agendamentos.create') }}" 
                       class="px-6 py-2 bg-white text-vm-gold font-bold rounded-lg shadow-lg hover:shadow-xl hover:bg-gray-50 transition-all flex items-center gap-2 border-2 border-white/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Agendar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Grade de Agenda -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b-2 border-gray-300">
                        <th class="sticky left-0 z-20 bg-gray-100 px-4 py-3 text-left text-xs font-bold text-gray-900 uppercase tracking-wider border-r-2 border-gray-300 min-w-[80px] shadow-sm">
                            Horário
                        </th>
                        @php
                            $profissionaisParaExibir = $profissionalId 
                                ? $profissionais->where('id', $profissionalId) 
                                : $profissionais;
                        @endphp
                        @foreach($profissionaisParaExibir as $prof)
                            <th class="px-4 py-3 text-center border-r border-gray-300 min-w-[200px] bg-gray-50">
                                <div class="flex flex-col items-center gap-2">
                                    <x-avatar 
                                        :src="$prof->avatar_url" 
                                        :name="$prof->nome" 
                                        size="md" />
                                    <span class="text-sm font-bold text-gray-900">{{ $prof->nome }}</span>
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Coletar todos os horários que têm agendamentos
                        $horariosComAgendamentos = [];
                        foreach ($agendamentos as $ag) {
                            $horaInicio = $ag->data_hora->format('H:i');
                            $duracao = $ag->servico ? $ag->servico->duracao_minutos : 30;
                            $horaFim = $ag->data_hora->copy()->addMinutes($duracao)->format('H:i');
                            
                            // Adicionar horário de início
                            if (!in_array($horaInicio, $horariosComAgendamentos)) {
                                $horariosComAgendamentos[] = $horaInicio;
                            }
                            
                            // Adicionar horários intermediários (a cada 30min)
                            $horaAtual = \Carbon\Carbon::createFromFormat('H:i', $horaInicio);
                            $horaFimObj = \Carbon\Carbon::createFromFormat('H:i', $horaFim);
                            while ($horaAtual->lt($horaFimObj)) {
                                $horaStr = $horaAtual->format('H:i');
                                if (!in_array($horaStr, $horariosComAgendamentos)) {
                                    $horariosComAgendamentos[] = $horaStr;
                                }
                                $horaAtual->addMinutes(30);
                            }
                        }
                        
                        // Se não houver agendamentos, mostrar horários padrão (9h-18h)
                        if (empty($horariosComAgendamentos)) {
                            for ($h = 9; $h < 18; $h++) {
                                $horariosComAgendamentos[] = sprintf('%02d:00', $h);
                                $horariosComAgendamentos[] = sprintf('%02d:30', $h);
                            }
                        } else {
                            // Ordenar horários
                            sort($horariosComAgendamentos);
                            
                            // Adicionar contexto: 1 hora antes do primeiro e 1 hora depois do último
                            $primeiroHorario = \Carbon\Carbon::createFromFormat('H:i', $horariosComAgendamentos[0]);
                            $ultimoHorario = \Carbon\Carbon::createFromFormat('H:i', $horariosComAgendamentos[count($horariosComAgendamentos) - 1]);
                            
                            $horariosComContexto = [];
                            // Adicionar 1 hora antes
                            $horaContexto = $primeiroHorario->copy()->subHour();
                            for ($i = 0; $i < 2; $i++) {
                                $horariosComContexto[] = $horaContexto->format('H:i');
                                $horaContexto->addMinutes(30);
                            }
                            
                            // Adicionar horários com agendamentos
                            $horariosComContexto = array_merge($horariosComContexto, $horariosComAgendamentos);
                            
                            // Adicionar 1 hora depois
                            $horaContexto = $ultimoHorario->copy()->addMinutes(30);
                            for ($i = 0; $i < 2; $i++) {
                                $horariosComContexto[] = $horaContexto->format('H:i');
                                $horaContexto->addMinutes(30);
                            }
                            
                            $horariosComAgendamentos = array_unique($horariosComContexto);
                            sort($horariosComAgendamentos);
                        }
                        
                        // Agrupar agendamentos por profissional e horário
                        $agendamentosPorProf = [];
                        foreach ($agendamentos as $ag) {
                            $profId = $ag->profissional_id;
                            if (!isset($agendamentosPorProf[$profId])) {
                                $agendamentosPorProf[$profId] = [];
                            }
                            $agendamentosPorProf[$profId][] = $ag;
                        }
                    @endphp
                    
                    @foreach($horariosComAgendamentos as $horario)
                        @php
                            // Verificar se este horário tem algum agendamento em qualquer profissional
                            $temAgendamento = false;
                            foreach ($profissionaisParaExibir as $prof) {
                                $ag = collect($agendamentosPorProf[$prof->id] ?? [])
                                    ->first(function($ag) use ($horario) {
                                        $horaAgendamento = $ag->data_hora->format('H:i');
                                        $duracao = $ag->servico ? $ag->servico->duracao_minutos : 30;
                                        $horaFim = $ag->data_hora->copy()->addMinutes($duracao)->format('H:i');
                                        
                                        // Verificar se o horário está dentro do intervalo do agendamento
                                        return $horaAgendamento <= $horario && $horario < $horaFim;
                                    });
                                if ($ag) {
                                    $temAgendamento = true;
                                    break;
                                }
                            }
                            
                            // Altura da linha: 30px se estiver vazia (mais compacta), 60px se tiver agendamento
                            $alturaLinha = $temAgendamento ? 60 : 30;
                        @endphp
                        <tr class="border-b border-gray-200 {{ $temAgendamento ? 'hover:bg-gray-50 bg-white' : 'bg-gray-50/50' }} transition-colors">
                            <td class="sticky left-0 z-10 {{ $temAgendamento ? 'bg-white' : 'bg-gray-50/50' }} px-4 py-2 text-sm font-bold text-gray-900 border-r-2 border-gray-300 shadow-sm">
                                {{ $horario }}
                            </td>
                            @foreach($profissionaisParaExibir as $prof)
                                <td class="px-2 py-1 border-r border-gray-200 relative {{ $temAgendamento ? 'bg-white' : 'bg-gray-50/50' }}" style="height: {{ $alturaLinha }}px;">
                                    @php
                                        // Encontrar agendamento que começa neste horário
                                        $agendamentoPrincipal = collect($agendamentosPorProf[$prof->id] ?? [])
                                            ->first(function($ag) use ($horario) {
                                                $horaAgendamento = $ag->data_hora->format('H:i');
                                                return $horaAgendamento == $horario;
                                            });
                                    @endphp
                                    
                                    @if($agendamentoPrincipal)
                                        @php
                                            $duracao = $agendamentoPrincipal->servico ? $agendamentoPrincipal->servico->duracao_minutos : 30;
                                            // Calcular quantos slots de 30min o agendamento ocupa
                                            $slots = max(1, ceil($duracao / 30));
                                            
                                            // Calcular altura: considerar que linhas vazias têm 30px e linhas com agendamento têm 60px
                                            // Mas para simplificar, vamos usar 60px por slot quando há agendamento
                                            $alturaTotal = ($slots * 60) - 8; // 60px por slot, menos 8px de margem
                                            $coresStatus = match($agendamentoPrincipal->status) {
                                                'concluido' => [
                                                    'bg' => 'bg-green-600',
                                                    'text' => 'text-white',
                                                    'border' => 'border-green-700'
                                                ],
                                                'pre_concluido' => [
                                                    'bg' => 'bg-orange-500',
                                                    'text' => 'text-white',
                                                    'border' => 'border-orange-600'
                                                ],
                                                'agendado' => [
                                                    'bg' => 'bg-blue-600',
                                                    'text' => 'text-white',
                                                    'border' => 'border-blue-700'
                                                ],
                                                default => [
                                                    'bg' => 'bg-gray-500',
                                                    'text' => 'text-white',
                                                    'border' => 'border-gray-600'
                                                ]
                                            };
                                        @endphp
                                        <div class="absolute top-1 left-1 right-1 rounded-lg shadow-xl border-2 {{ $coresStatus['bg'] }} {{ $coresStatus['text'] }} {{ $coresStatus['border'] }} p-2 overflow-hidden cursor-pointer hover:shadow-2xl hover:scale-[1.02] transition-all group"
                                             style="height: {{ $alturaTotal }}px; z-index: 5;"
                                             onclick="window.location.href='{{ route('agendamentos.edit', $agendamentoPrincipal) }}'"
                                             title="{{ $agendamentoPrincipal->nome_cliente }} - {{ $agendamentoPrincipal->servico ? $agendamentoPrincipal->servico->nome : 'Serviço' }}">
                                            <div class="flex flex-col h-full">
                                                <div class="font-bold text-xs sm:text-sm truncate drop-shadow-md" style="text-shadow: 0 1px 2px rgba(0,0,0,0.3);">
                                                    {{ strtoupper(substr($agendamentoPrincipal->nome_cliente, 0, 20)) }}
                                                </div>
                                                <div class="text-xs font-semibold mt-1 drop-shadow-md" style="text-shadow: 0 1px 2px rgba(0,0,0,0.3);">
                                                    {{ $agendamentoPrincipal->data_hora->format('H:i') }} - {{ $agendamentoPrincipal->data_hora->copy()->addMinutes($duracao)->format('H:i') }}
                                                </div>
                                                @if($agendamentoPrincipal->servico)
                                                    <div class="text-xs font-medium mt-1 truncate drop-shadow-md" style="text-shadow: 0 1px 2px rgba(0,0,0,0.3);">
                                                        {{ $agendamentoPrincipal->servico->nome }}
                                                    </div>
                                                @endif
                                                <div class="flex items-center gap-1 mt-auto">
                                                    @if($agendamentoPrincipal->status == 'agendado')
                                                        <span class="text-xs bg-white/30 backdrop-blur-sm px-2 py-0.5 rounded font-semibold border border-white/40">⏰</span>
                                                    @elseif($agendamentoPrincipal->status == 'pre_concluido')
                                                        <span class="text-xs bg-white/30 backdrop-blur-sm px-2 py-0.5 rounded font-semibold border border-white/40">⏳</span>
                                                    @elseif($agendamentoPrincipal->status == 'concluido')
                                                        <span class="text-xs bg-white/30 backdrop-blur-sm px-2 py-0.5 rounded font-semibold border border-white/40">✓</span>
                                                    @endif
                                                    <span class="text-xs font-semibold ml-auto drop-shadow-md" style="text-shadow: 0 1px 2px rgba(0,0,0,0.3);">
                                                        {{ $duracao }}min
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Legenda -->
    <div class="mt-4 bg-white rounded-lg shadow-md p-4">
        <div class="flex flex-wrap items-center gap-4">
            <span class="text-sm font-semibold text-gray-700">Legenda:</span>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-blue-500 rounded"></div>
                <span class="text-sm text-gray-600">Agendado</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-orange-500 rounded"></div>
                <span class="text-sm text-gray-600">Pré-Concluído</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-green-500 rounded"></div>
                <span class="text-sm text-gray-600">Concluído</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-gray-400 rounded"></div>
                <span class="text-sm text-gray-600">Cancelado</span>
            </div>
        </div>
    </div>
</div>

<style>
    /* Scrollbar personalizada */
    .overflow-x-auto::-webkit-scrollbar {
        height: 8px;
    }
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #D4AF37;
        border-radius: 4px;
    }
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #B8941F;
    }
</style>
@endsection
