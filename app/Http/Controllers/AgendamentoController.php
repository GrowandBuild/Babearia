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
        
        return view('agendamentos.auto-agendar', compact('profissionais', 'servicos'));
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

    public function storeAutoAgendamento(Request $request)
    {
        $validated = $request->validate([
            'profissional_id' => 'required|integer|exists:profissionais,id',
            'servico_id' => 'required|integer|exists:servicos,id',
            'data' => 'required|date',
            'hora' => 'required',
            'cliente_avulso' => 'required|string|max:255',
            'observacoes' => 'nullable|string',
        ]);

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

        // Converter string vazia para null no cliente_id
        $request->merge(['cliente_id' => null]);

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

