<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profissional;
use App\Models\Agendamento;
use App\Models\FormaPagamento;
use App\Models\Pagamento;

class AdminAgendaController extends Controller
{
    // Tela do dashboard de agenda (admin)
    public function index()
    {
        return view('admin.agenda');
    }

    // Endpoint que retorna profissionais (resources) e eventos
    public function events(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        // carregar profissionais ativos
        $professionals = Profissional::with('user')->where('ativo', true)->get();

        $resources = $professionals->map(function ($p) {
            return [
                'id' => (int)$p->id,
                'title' => $p->getNomeAttribute(),
                'avatar' => $p->getAvatarUrlAttribute(),
            ];
        })->values();

        // Buscar agendamentos no intervalo (se informado)
        $query = Agendamento::with(['servicos', 'cliente', 'profissional.user']);
        if ($start) $query->where('data_hora', '>=', $start);
        if ($end) $query->where('data_hora', '<=', $end);

        $agendamentos = $query->get();

        $events = $agendamentos->map(function ($a) {
            // duração: soma dos serviços ou 30 minutos padrão
            $duracao = 0;
            if ($a->servicos && $a->servicos->count() > 0) {
                foreach ($a->servicos as $s) {
                    $duracao += (int)($s->duracao_minutos ?? 0);
                }
            }
            if ($duracao <= 0) $duracao = 30;

            $start = $a->data_hora->format('c');
            $end = $a->data_hora->copy()->addMinutes($duracao)->format('c');

            $title = $a->getNomeClienteAttribute();
            $service = $a->servico ? $a->servico->nome : null;
            if ($service) $title .= ' — ' . $service;

            $color = '#3B82F6'; // padrão azul
            if ($a->status === 'concluido') $color = '#10B981';
            if ($a->status === 'cancelado') $color = '#EF4444';

            return [
                'id' => (int)$a->id,
                'resourceId' => (int)$a->profissional_id,
                'title' => $title,
                'start' => $start,
                'end' => $end,
                'color' => $color,
                'extendedProps' => [
                    'status' => $a->status,
                    'observacoes' => $a->observacoes,
                    'servicos' => $a->servicos->map(function ($s) {
                        return [
                            'nome' => $s->nome,
                            'preco' => $s->preco_cobrado ?? $s->preco,
                        ];
                    })->toArray(),
                ],
            ];
        })->values();

        return response()->json([
            'resources' => $resources,
            'events' => $events,
        ]);
    }

    // Finalizar atendimento
    public function finalizarAtendimento(Request $request)
    {
        $request->validate([
            'agendamento_id' => 'required|exists:agendamentos,id',
            'servicos_adicionais' => 'nullable|array',
            'servicos_adicionais.*' => 'exists:servicos,id',
            'desconto' => 'nullable|numeric|min:0',
            'forma_pagamento' => 'required|string|in:pix,dinheiro,debito,credito',
            'observacoes_finalizacao' => 'nullable|string',
        ]);

        $agendamento = Agendamento::with(['servicos', 'cliente', 'pagamentos'])->findOrFail($request->agendamento_id);

        // Verificar se já foi finalizado
        if ($agendamento->status === 'concluido') {
            return response()->json(['error' => 'Atendimento já foi finalizado'], 400);
        }

        // Calcular valor total
        $valorTotal = 0;
        $valorServicosOriginais = 0;

        // Valor dos serviços originais
        foreach ($agendamento->servicos as $servico) {
            $valorServicosOriginais += $servico->preco_cobrado ?? 0;
        }

        // Verificar se é cliente com pacote
        $isPacote = false;
        if ($agendamento->cliente && $agendamento->cliente->isPackageValid()) {
            $isPacote = true;
            $valorServicosOriginais = 0; // Pacote já foi pago
        }

        // Adicionar serviços extras
        $valorExtras = 0;
        if ($request->servicos_adicionais) {
            foreach ($request->servicos_adicionais as $servicoId) {
                $servico = \App\Models\Servico::find($servicoId);
                if ($servico) {
                    $valorExtras += $servico->preco;
                    // Vincular serviço extra ao agendamento
                    $agendamento->servicos()->attach($servicoId, [
                        'preco_cobrado' => $servico->preco
                    ]);
                }
            }
        }

        $valorTotal = $valorServicosOriginais + $valorExtras;
        
        // Aplicar desconto
        $desconto = $request->desconto ?? 0;
        $valorFinal = max(0, $valorTotal - $desconto);

        // Atualizar status do agendamento
        $agendamento->status = 'concluido';
        $agendamento->observacoes_finalizacao = $request->observacoes_finalizacao;
        $agendamento->save();

        // Se for pacote, usar um serviço do pacote
        if ($isPacote && $agendamento->cliente) {
            $agendamento->cliente->usePackageService();
        }

        // Criar registro de pagamento
        if ($valorFinal > 0) {
            $formaPagamento = FormaPagamento::whereRaw('LOWER(nome) = ?', [strtolower($request->forma_pagamento)])->first();
            if (! $formaPagamento) {
                return response()->json(['error' => 'Forma de pagamento inválida'], 400);
            }

            $profissional = $agendamento->profissional;
            $valoresPagamento = Pagamento::calcularValores($valorFinal, $formaPagamento, $profissional, 0);

            $pagamento = Pagamento::create(array_merge(
                [
                    'agendamento_id' => $agendamento->id,
                    'forma_pagamento_id' => $formaPagamento->id,
                ],
                $valoresPagamento
            ));
        }

        return response()->json([
            'success' => true,
            'message' => 'Atendimento finalizado com sucesso',
            'valor_final' => $valorFinal,
            'foi_pacote' => $isPacote
        ]);
    }

    // Mostrar página de finalização
    public function mostrarFinalizar($id)
    {
        $agendamento = Agendamento::with(['servicos', 'cliente', 'profissional'])->findOrFail($id);
        
        // Verificar se já foi finalizado
        if ($agendamento->status === 'concluido') {
            return redirect()->route('admin.agenda')
                ->with('info', 'Este atendimento já foi finalizado');
        }
        
        return view('admin.finalizar-agendamento', compact('agendamento'));
    }
}
