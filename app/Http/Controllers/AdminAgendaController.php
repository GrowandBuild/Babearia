<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profissional;
use App\Models\Agendamento;

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
                ],
            ];
        })->values();

        return response()->json([
            'resources' => $resources,
            'events' => $events,
        ]);
    }
}
