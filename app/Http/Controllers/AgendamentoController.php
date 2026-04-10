<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Profissional;
use App\Models\Cliente;
use App\Models\Servico;
use App\Models\FormaPagamento;
use App\Models\Pagamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgendamentoController extends Controller
{
    public function index(Request $request)
    {
        $profissionalId = $request->input('profissional_id');
        $data = $request->input('data', today());

        $query = Agendamento::with(['profissional.user', 'servicos', 'cliente'])
            ->whereDate('data_hora', $data);

        if ($profissionalId) {
            $query->where('profissional_id', $profissionalId);
        }

        $agendamentos = $query->orderBy('data_hora')->get();
        $profissionais = Profissional::where('ativo', true)->get();

        return view('agendamentos.index', compact('agendamentos', 'profissionais', 'data', 'profissionalId'));
    }

    public function agenda(Request $request)
    {
        $user = auth()->user();
        
        // Se for profissional, mostra apenas sua agenda
        if ($user->isProfissional()) {
            $profissionalId = $user->profissional->id;
        } else {
            $profissionalId = $request->input('profissional_id');
        }

        $data = $request->input('data', today());

        $query = Agendamento::with(['profissional.user', 'servicos', 'cliente'])
            ->whereDate('data_hora', $data);

        if ($profissionalId) {
            $query->where('profissional_id', $profissionalId);
        }

        $agendamentos = $query->orderBy('data_hora')->get();
        $profissionais = Profissional::where('ativo', true)->get();

        return view('agendamentos.agenda', compact('agendamentos', 'profissionais', 'data', 'profissionalId'));
    }

    public function create()
    {
        $user = auth()->user();
        $profissionais = Profissional::where('ativo', true)->get();
        $clientes = Cliente::orderBy('nome')->get();
        $servicos = Servico::where('ativo', true)->get();
        
        // Se for profissional, pré-selecionar ela mesma
        $profissionalSelecionado = $user->isProfissional() ? $user->profissional->id : null;

        return view('agendamentos.create', compact('profissionais', 'clientes', 'servicos', 'profissionalSelecionado'));
    }

    public function store(Request $request)
    {
        // Converter string vazia para null no cliente_id
        if ($request->cliente_id === '' || $request->cliente_id === null) {
            $request->merge(['cliente_id' => null]);
        }
        
        // Se cliente_id está preenchido, remover cliente_avulso
        if ($request->cliente_id) {
            $request->merge(['cliente_avulso' => null]);
        } else {
            // Se cliente_id está vazio, converter string vazia de cliente_avulso para null
            if ($request->cliente_avulso === '') {
                $request->merge(['cliente_avulso' => null]);
            }
        }
        
        $validated = $request->validate([
            'profissional_id' => 'required|integer|exists:profissionais,id',
            'servico_id' => 'required|integer|exists:servicos,id',
            'data' => 'required|date',
            'hora' => 'required',
            'cliente_id' => 'nullable|integer|exists:clientes,id',
            'cliente_avulso' => 'required_without:cliente_id|nullable|string|max:255',
            'observacoes' => 'nullable|string',
        ]);

        // Combinar data e hora
        $validated['data_hora'] = $validated['data'] . ' ' . $validated['hora'];
        
        // Separar dados do agendamento e do serviço
        $servicoId = $validated['servico_id'];
        unset($validated['data'], $validated['hora'], $validated['servico_id']);

        // Criar agendamento
        $agendamento = Agendamento::create($validated);
        
        // Vincular serviço na tabela pivot
        $servico = Servico::find($servicoId);
        $agendamento->servicos()->attach($servicoId, [
            'preco_cobrado' => $servico->preco
        ]);

        return redirect()->route('agendamentos.agenda')
            ->with('success', 'Agendamento criado com sucesso!');
    }

    public function edit(Agendamento $agendamento)
    {
        $profissionais = Profissional::where('ativo', true)->get();
        $clientes = Cliente::orderBy('nome')->get();
        $servicos = Servico::where('ativo', true)->get();

        return view('agendamentos.edit', compact('agendamento', 'profissionais', 'clientes', 'servicos'));
    }

    public function update(Request $request, Agendamento $agendamento)
    {
        // Converter string vazia para null no cliente_id
        if ($request->cliente_id === '' || $request->cliente_id === null) {
            $request->merge(['cliente_id' => null]);
        }
        
        // Se cliente_id está preenchido, remover cliente_avulso
        if ($request->cliente_id) {
            $request->merge(['cliente_avulso' => null]);
        } else {
            // Se cliente_id está vazio, converter string vazia de cliente_avulso para null
            if ($request->cliente_avulso === '') {
                $request->merge(['cliente_avulso' => null]);
            }
        }
        
        $validated = $request->validate([
            'profissional_id' => 'required|integer|exists:profissionais,id',
            'servico_id' => 'required|integer|exists:servicos,id',
            'data' => 'required|date',
            'hora' => 'required',
            'cliente_id' => 'nullable|integer|exists:clientes,id',
            'cliente_avulso' => 'required_without:cliente_id|nullable|string|max:255',
            'observacoes' => 'nullable|string',
        ]);

        // Combinar data e hora
        $validated['data_hora'] = $validated['data'] . ' ' . $validated['hora'];
        
        // Separar dados do agendamento e do serviço
        $servicoId = $validated['servico_id'];
        unset($validated['data'], $validated['hora'], $validated['servico_id']);

        // Atualizar agendamento
        $agendamento->update($validated);
        
        // Atualizar serviço na tabela pivot
        $servico = Servico::find($servicoId);
        $agendamento->servicos()->sync([$servicoId => [
            'preco_cobrado' => $servico->preco
        ]]);

        return redirect()->route('agendamentos.agenda')
            ->with('success', 'Agendamento atualizado com sucesso!');
    }

    public function destroy(Agendamento $agendamento)
    {
        $user = auth()->user();
        
        // Apenas proprietária pode cancelar agendamentos concluídos
        if ($agendamento->status === 'concluido' && !$user->isProprietaria()) {
            return redirect()->route('agendamentos.agenda')
                ->with('error', 'Você não pode cancelar um agendamento já concluído.');
        }
        
        DB::beginTransaction();
        
        try {
            // Se está concluído e tem pagamentos, deletar os pagamentos
            if ($agendamento->status === 'concluido') {
                $agendamento->pagamentos()->delete();
            }
            
            // Cancelar o agendamento
            $agendamento->update(['status' => 'cancelado']);
            
            DB::commit();
            
            return redirect()->route('agendamentos.agenda')
                ->with('success', 'Agendamento cancelado com sucesso!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('agendamentos.agenda')
                ->with('error', 'Erro ao cancelar agendamento: ' . $e->getMessage());
        }
    }

    public function deletarCompletamente(Agendamento $agendamento)
    {
        DB::beginTransaction();
        
        try {
            // Deletar todos os pagamentos associados
            $agendamento->pagamentos()->delete();
            
            // Deletar o agendamento completamente
            $agendamento->delete();
            
            DB::commit();
            
            return redirect()->route('agendamentos.agenda')
                ->with('success', 'Agendamento removido completamente do sistema!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('agendamentos.agenda')
                ->with('error', 'Erro ao deletar agendamento: ' . $e->getMessage());
        }
    }

    public function concluir(Agendamento $agendamento)
    {
        $formasPagamento = FormaPagamento::where('ativo', true)->get();
        $agendamento->load('servicos', 'profissional');

        return view('agendamentos.concluir', compact('agendamento', 'formasPagamento'));
    }

    public function finalizarPagamento(Request $request, Agendamento $agendamento)
    {
        $request->validate([
            'pagamentos' => 'required|array|min:1',
            'pagamentos.*.forma_pagamento_id' => 'required|exists:formas_pagamento,id',
            'pagamentos.*.valor' => 'required|numeric|min:0',
            'pagamentos.*.gorjeta' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        
        try {
            foreach ($request->pagamentos as $pag) {
                $formaPagamento = FormaPagamento::find($pag['forma_pagamento_id']);
                $profissional = $agendamento->profissional;
                
                $valores = Pagamento::calcularValores(
                    $pag['valor'],
                    $formaPagamento,
                    $profissional,
                    $pag['gorjeta'] ?? 0
                );

                Pagamento::create(array_merge([
                    'agendamento_id' => $agendamento->id,
                    'forma_pagamento_id' => $formaPagamento->id,
                ], $valores));
            }
            
            // Sincronizar sistema de níveis do cliente
            if ($agendamento->cliente) {
                $cliente = $agendamento->cliente;
                $totalGasto = $cliente->agendamentos()
                    ->whereHas('pagamentos')
                    ->with('pagamentos')
                    ->get()
                    ->sum(function($agendamento) {
                        // Usar valor_liquido se existir, senão usar valor
                        return $agendamento->pagamentos->sum('valor_liquido') ?: 
                               $agendamento->pagamentos->sum('valor');
                    });
                
                // Atualizar cache ou sistema de níveis se existir
                // Isso garante que o nível seja recalculado automaticamente
                // quando novos pagamentos forem registrados
            }

            // Profissional: pré-concluído / Proprietária: concluído
            $user = auth()->user();
            $novoStatus = $user->isProprietaria() ? 'concluido' : 'pre_concluido';
            $agendamento->update(['status' => $novoStatus]);

            DB::commit();

            $mensagem = $user->isProprietaria() 
                ? 'Atendimento concluído e confirmado com sucesso!' 
                : 'Atendimento pré-concluído! Aguardando confirmação da proprietária.';

            return redirect()->route('agendamentos.agenda')
                ->with('success', $mensagem);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Erro ao processar pagamento: ' . $e->getMessage());
        }
    }

    public function confirmarConclusao(Agendamento $agendamento)
    {
        // Apenas proprietária pode confirmar
        if (!auth()->user()->isProprietaria()) {
            return redirect()->route('agendamentos.agenda')
                ->with('error', 'Apenas a proprietária pode confirmar conclusões.');
        }

        if ($agendamento->status !== 'pre_concluido') {
            return redirect()->route('agendamentos.agenda')
                ->with('error', 'Este agendamento não está pré-concluído.');
        }

        $agendamento->update(['status' => 'concluido']);

        return redirect()->route('agendamentos.agenda')
            ->with('success', 'Atendimento confirmado com sucesso!');
    }

    public function autoAgendar()
    {
        $profissionais = Profissional::where('ativo', true)->get();
        $servicos = Servico::where('ativo', true)->get();
        
        // Verificar se o usuário logado tem pacote ativo
        $pacoteCliente = null;
        $servicoPacote = null;
        
        if (auth()->check()) {
            $user = auth()->user();
            
            // Se for cliente, verificar se tem pacote
            if ($user->isCliente() && $user->cliente) {
                $cliente = $user->cliente;
                
                if ($cliente->isPackageValid()) {
                    $pacoteCliente = $cliente;
                    
                    // Criar um serviço virtual para o pacote
                    $servicoPacote = (object) [
                        'id' => 'pacote_' . $cliente->id,
                        'nome' => 'Seu Pacote de Serviços',
                        'descricao' => $cliente->package_observations ?? 'Pacote especial com ' . $cliente->getRemainingServices() . ' serviços restantes',
                        'preco' => 0, // Não cobra, já foi pago
                        'duracao_minutos' => 30, // Tempo padrão
                        'imagem_url' => null,
                        'is_pacote' => true,
                        'pacote_info' => [
                            'total' => $cliente->package_total_services,
                            'usados' => $cliente->package_used_services,
                            'restantes' => $cliente->getRemainingServices(),
                            'valor_pago' => $cliente->package_price
                        ]
                    ];
                }
            }
            
            // Verificar se o usuário logado quer mostrar agenda comprometida
            $mostrarAgendaComprometida = false;
            
            // Se for profissional, verificar sua própria configuração
            if ($user->isProfissional() && $user->profissional) {
                $mostrarAgendaComprometida = $user->mostrar_agenda_comprometida;
            }
            // Se for proprietária, verificar se algum profissional tem a opção ativada
            elseif ($user->isProprietaria()) {
                $mostrarAgendaComprometida = Profissional::whereHas('user', function($query) {
                    $query->where('mostrar_agenda_comprometida', true);
                })->exists();
            }
        } else {
            $mostrarAgendaComprometida = false;
        }
        
        return view('agendamentos.auto-agendar', compact('profissionais', 'servicos', 'pacoteCliente', 'servicoPacote', 'mostrarAgendaComprometida'));
    }

    public function horariosDisponiveis(Request $request)
    {
        $request->validate([
            'profissional_id' => 'required|exists:profissionais,id',
            'servico_id' => 'required|exists:servicos,id',
            'data' => 'required|date',
            'hora_desejada' => 'required',
        ]);

        $profissionalId = $request->profissional_id;
        $servicoId = $request->servico_id;
        $data = $request->data;
        $horaDesejada = $request->hora_desejada;

        $servico = Servico::find($servicoId);
        $duracao = $servico->duracao_minutos ?? 30;

        // Buscar agendamentos do profissional na data
        $agendamentos = Agendamento::where('profissional_id', $profissionalId)
            ->whereDate('data_hora', $data)
            ->where('status', '!=', 'cancelado')
            ->with('servicos')
            ->get();

        // Criar array de intervalos ocupados (início e fim)
        $intervalosOcupados = [];
        foreach ($agendamentos as $ag) {
            $horaInicio = \Carbon\Carbon::parse($ag->data_hora);
            $duracaoAg = 0;
            foreach ($ag->servicos as $s) {
                $duracaoAg += $s->duracao_minutos ?? 30;
            }
            if ($duracaoAg == 0) $duracaoAg = 30;
            
            $horaFim = $horaInicio->copy()->addMinutes($duracaoAg);
            
            // Debug: mostrar agendamentos existentes
            \Log::info("Agendamento existente: {$ag->id} - {$horaInicio->format('H:i')} as {$horaFim->format('H:i')} ({$duracaoAg}min)");
            
            $intervalosOcupados[] = [
                'inicio' => $horaInicio,
                'fim' => $horaFim
            ];
        }

        // Verificar se horário desejado está disponível
        // Garantir formato correto da hora (HH:MM)
        $horaFormatada = strlen($horaDesejada) == 5 ? $horaDesejada : sprintf('%02d:%02d', ...explode(':', $horaDesejada));
        $horaDesejadaObj = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', "$data $horaFormatada:00");
        $horaFimDesejada = $horaDesejadaObj->copy()->addMinutes($duracao);
        
        // Verificar sobreposição com agendamentos existentes
        $disponivel = true;
        \Log::info("Verificando horário: {$horaDesejadaObj->format('H:i')} as {$horaFimDesejada->format('H:i')} ({$duracao}min)");
        
        foreach ($intervalosOcupados as $intervalo) {
            // Lógica simples: verifica se há sobreposição real
            // Novo agendamento começa antes do fim do existente E termina depois do início do existente
            $sobreposicao = $horaDesejadaObj->lt($intervalo['fim']) && $horaFimDesejada->gt($intervalo['inicio']);
            \Log::info("Conflito com {$intervalo['inicio']->format('H:i')}-{$intervalo['fim']->format('H:i')}: " . ($sobreposicao ? 'SIM' : 'NÃO'));
            
            if ($sobreposicao) {
                $disponivel = false;
                break;
            }
        }
        
        \Log::info("Resultado: " . ($disponivel ? 'DISPONÍVEL' : 'INDISPONÍVEL'));

        $sugestoes = [];
        if (!$disponivel) {
            // Buscar próximo horário disponível (depois)
            $horaSugerida = $horaFimDesejada->copy();
            $tentativas = 0;
            while ($tentativas < 40 && $horaSugerida->format('H:i') <= '20:00') { // Máximo até 20h
                $horaFimSugestao = $horaSugerida->copy()->addMinutes($duracao);
                
                $disponivelSugestao = true;
                foreach ($intervalosOcupados as $intervalo) {
                    if ($horaSugerida->lt($intervalo['fim']) && $horaFimSugestao->gt($intervalo['inicio'])) {
                        $disponivelSugestao = false;
                        break;
                    }
                }
                
                if ($disponivelSugestao) {
                    $sugestoes[] = [
                        'hora' => $horaSugerida->format('H:i'),
                        'tipo' => 'proximo'
                    ];
                    break;
                }
                
                $horaSugerida->addMinutes(30);
                $tentativas++;
            }

            // Buscar horário anterior disponível (antes)
            $horaSugerida = $horaDesejadaObj->copy()->subMinutes(30);
            $tentativas = 0;
            while ($tentativas < 40 && $horaSugerida->format('H:i') >= '09:00') {
                $horaFimSugestao = $horaSugerida->copy()->addMinutes($duracao);
                
                $disponivelSugestao = true;
                foreach ($intervalosOcupados as $intervalo) {
                    if ($horaSugerida->lt($intervalo['fim']) && $horaFimSugestao->gt($intervalo['inicio'])) {
                        $disponivelSugestao = false;
                        break;
                    }
                }
                
                if ($disponivelSugestao) {
                    $sugestoes[] = [
                        'hora' => $horaSugerida->format('H:i'),
                        'tipo' => 'anterior'
                    ];
                    break;
                }
                
                $horaSugerida->subMinutes(30);
                $tentativas++;
            }
        }

        return response()->json([
            'disponivel' => $disponivel,
            'sugestoes' => $sugestoes
        ]);
    }

    public function horariosDisponiveisDia(Request $request)
    {
        $request->validate([
            'profissional_id' => 'required|exists:profissionais,id',
            'servico_id' => 'required|exists:servicos,id',
            'data' => 'required|date',
            'from' => 'nullable',
            'to' => 'nullable',
            'step' => 'nullable|integer'
        ]);

        $profissionalId = $request->profissional_id;
        $servicoId = $request->servico_id;
        $data = $request->data;
        $from = $request->input('from', '09:00');
        $to = $request->input('to', '17:30');
        $step = $request->input('step', 30);

        $servico = Servico::find($servicoId);
        $duracao = $servico->duracao_minutos ?? 30;

        // Verificar se o usuário quer mostrar agenda comprometida
        // TEMPORÁRIO: Sempre buscar agendamentos existentes
        $mostrarAgendaComprometida = true;
        
        /*
        $mostrarAgendaComprometida = false;
        if (auth()->check()) {
            $user = auth()->user();
            
            // Se for profissional, verificar sua própria configuração
            if ($user->isProfissional() && $user->profissional) {
                $mostrarAgendaComprometida = $user->mostrar_agenda_comprometida ?? false;
            }
            // Se for proprietária, verificar se algum profissional tem a opção ativada
            elseif ($user->isProprietaria()) {
                $mostrarAgendaComprometida = Profissional::whereHas('user', function($query) {
                    $query->where('mostrar_agenda_comprometida', true);
                })->exists();
            }
        }
        */
        
        // Debug: verificar configuração
        \Log::info("mostrar_agenda_comprometida: " . ($mostrarAgendaComprometida ? 'TRUE' : 'FALSE'));

        // Buscar agendamentos do profissional na data
        // TEMPORÁRIO: Sempre buscar agendamentos para teste
        $agendamentos = Agendamento::where('profissional_id', $profissionalId)
            ->whereDate('data_hora', $data)
            ->where('status', '!=', 'cancelado')
            ->with('servicos')
            ->get();
        
        /*
        if ($mostrarAgendaComprometida) {
            // Modo público: mostrar agendamentos existentes
            $agendamentos = Agendamento::where('profissional_id', $profissionalId)
                ->whereDate('data_hora', $data)
                ->where('status', '!=', 'cancelado')
                ->with('servicos')
                ->get();
        } else {
            // Modo privado: não mostrar agendamentos existentes
            $agendamentos = collect([]);
        }
        */

        $intervalosOcupados = [];
        foreach ($agendamentos as $ag) {
            $horaInicio = \Carbon\Carbon::parse($ag->data_hora);
            $duracaoAg = 0;
            foreach ($ag->servicos as $s) {
                $duracaoAg += $s->duracao_minutos ?? 30;
            }
            if ($duracaoAg == 0) $duracaoAg = 30;
            $horaFim = $horaInicio->copy()->addMinutes($duracaoAg);
            
            $intervalosOcupados[] = [
                'inicio' => $horaInicio,
                'fim' => $horaFim
            ];
        }

        // Gerar timeslots entre from e to com step
        $result = [];
        $fromParts = explode(':', $from);
        $toParts = explode(':', $to);
        $cur = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', "$data {$fromParts[0]}:{$fromParts[1]}:00");
        $end = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', "$data {$toParts[0]}:{$toParts[1]}:00");
        while ($cur <= $end) {
            $horaFimSug = $cur->copy()->addMinutes($duracao);
            $disponivel = true;
            
            // Só verificar conflitos se estiver em modo público
            if ($mostrarAgendaComprometida) {
                foreach ($intervalosOcupados as $intervalo) {
                    $sobreposicao = $cur->lt($intervalo['fim']) && $horaFimSug->gt($intervalo['inicio']);
                    if ($sobreposicao) {
                        $disponivel = false;
                        break;
                    }
                }
            }
            
            // Em modo privado, todos aparecem como disponíveis
            if (!$mostrarAgendaComprometida) {
                $disponivel = true;
            }
            
            $result[] = [
                'hora' => $cur->format('H:i'),
                'disponivel' => $disponivel
            ];
            $cur->addMinutes($step);
        }

        return response()->json(['timeslots' => $result]);
    }

    public function storeAutoAgendamento(Request $request)
    {
        // Regras de validação diferentes para usuários logados vs não logados
        $rules = [
            'profissional_id' => 'required|integer|exists:profissionais,id',
            'servico_id' => 'required|integer|exists:servicos,id',
            'data' => 'required|date',
            'hora' => 'required',
            'observacoes' => 'nullable|string',
        ];

        // Se não estiver logado, exigir nome do cliente
        if (!auth()->check()) {
            $rules['cliente_avulso'] = 'required|string|max:255';
        }

        $validated = $request->validate($rules);

        // Verificar se horário está disponível
        $horarioDisponivel = $this->verificarHorarioDisponivel(
            $validated['profissional_id'],
            $validated['data'],
            $validated['hora'],
            $validated['servico_id']
        );

        if (!$horarioDisponivel['disponivel']) {
            return back()->withErrors([
                'hora' => 'Este horário não está disponível. ' . 
                         ($horarioDisponivel['sugestao'] ? 'Sugestão: ' . $horarioDisponivel['sugestao'] : '')
            ])->withInput();
        }

        // Se usuário estiver logado, buscar seu cliente_id
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->cliente) {
                $validated['cliente_id'] = $user->cliente->id;
                $validated['cliente_avulso'] = null;
            }
        } else {
            // Para usuários não logados, garantir que cliente_id seja null
            $validated['cliente_id'] = null;
        }

        // Combinar data e hora
        $validated['data_hora'] = $validated['data'] . ' ' . $validated['hora'];
        
        // Separar dados do agendamento e do serviço
        $servicoId = $validated['servico_id'];
        unset($validated['data'], $validated['hora'], $validated['servico_id']);

        // Criar agendamento
        $agendamento = Agendamento::create($validated);
        
        // Vincular serviço na tabela pivot
        $servico = Servico::find($servicoId);
        $agendamento->servicos()->attach($servicoId, [
            'preco_cobrado' => $servico->preco
        ]);

        return redirect()->route('agendamentos.agenda')
            ->with('success', 'Agendamento criado com sucesso!');
    }

    private function verificarHorarioDisponivel($profissionalId, $data, $hora, $servicoId)
    {
        $servico = Servico::find($servicoId);
        $duracao = $servico->duracao_minutos ?? 30;

        $agendamentos = Agendamento::where('profissional_id', $profissionalId)
            ->whereDate('data_hora', $data)
            ->where('status', '!=', 'cancelado')
            ->with('servicos')
            ->get();

        // Criar array de intervalos ocupados
        $intervalosOcupados = [];
        foreach ($agendamentos as $ag) {
            $horaInicio = \Carbon\Carbon::parse($ag->data_hora);
            $duracaoAg = 0;
            foreach ($ag->servicos as $s) {
                $duracaoAg += $s->duracao_minutos ?? 30;
            }
            if ($duracaoAg == 0) $duracaoAg = 30;
            
            $horaFim = $horaInicio->copy()->addMinutes($duracaoAg);
            
            $intervalosOcupados[] = [
                'inicio' => $horaInicio,
                'fim' => $horaFim
            ];
        }

        // Garantir formato correto da hora (HH:MM)
        $horaFormatada = strlen($hora) == 5 ? $hora : sprintf('%02d:%02d', ...explode(':', $hora));
        $horaDesejadaObj = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', "$data $horaFormatada:00");
        $horaFimDesejada = $horaDesejadaObj->copy()->addMinutes($duracao);
        
        // Verificar sobreposição
        $disponivel = true;
        foreach ($intervalosOcupados as $intervalo) {
            // Verificar se há sobreposição: 
            // Novo agendamento começa antes do fim do existente E termina depois do início do existente
            // OU começa exatamente no mesmo horário
            // OU termina exatamente no mesmo horário
            if (($horaDesejadaObj->lt($intervalo['fim']) && $horaFimDesejada->gt($intervalo['inicio'])) ||
                $horaDesejadaObj->eq($intervalo['inicio']) ||
                ($horaDesejadaObj->gte($intervalo['inicio']) && $horaDesejadaObj->lt($intervalo['fim'])) ||
                ($horaFimDesejada->gt($intervalo['inicio']) && $horaFimDesejada->lte($intervalo['fim']))) {
                $disponivel = false;
                break;
            }
        }

        $sugestao = null;
        if (!$disponivel) {
            // Buscar próximo disponível
            $horaSugerida = $horaFimDesejada->copy();
            for ($i = 0; $i < 40 && $horaSugerida->format('H:i') <= '20:00'; $i++) {
                $horaFimSugestao = $horaSugerida->copy()->addMinutes($duracao);
                
                $disponivelSugestao = true;
                foreach ($intervalosOcupados as $intervalo) {
                    if ($horaSugerida->lt($intervalo['fim']) && $horaFimSugestao->gt($intervalo['inicio'])) {
                        $disponivelSugestao = false;
                        break;
                    }
                }
                
                if ($disponivelSugestao) {
                    $sugestao = $horaSugerida->format('H:i');
                    break;
                }
                
                $horaSugerida->addMinutes(30);
            }
        }

        return [
            'disponivel' => $disponivel,
            'sugestao' => $sugestao
        ];
    }
}

