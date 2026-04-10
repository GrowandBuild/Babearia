<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Servico;

class ServicosSeeder extends Seeder
{
    public function run()
    {
        // Serviços sincronizados automaticamente em 10/04/2026 13:16:53
        // Total de serviços: 1

        Servico::updateOrCreate(
            ['nome' => 'Degrade'],
            [
                'nome' => 'Degrade',
                'descricao' => 'Corte de cabelo degrade tradicional',
                'preco' => 28.00,
                'duracao_minutos' => 25,
                'ativo' => true,
            ]
        );

    }
}
