<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Servico;

class ServicosSeeder extends Seeder
{
    public function run()
    {
        // Serviços sincronizados automaticamente em 11/07/2026 14:51:33
        // Total de serviços: 3

        Servico::updateOrCreate(
            ['nome' => 'Degrade'],
            [
                'nome' => 'Degrade',
                'descricao' => 'Corte de cabelo degrade tradicional',
                'preco' => 28.00,
                'duracao' => ,
                'ativo' => true,
            ]
        );

        Servico::updateOrCreate(
            ['nome' => 'sdsds'],
            [
                'nome' => 'sdsds',
                'descricao' => 'dsds',
                'preco' => 19.00,
                'duracao' => ,
                'ativo' => true,
            ]
        );

        Servico::updateOrCreate(
            ['nome' => 'dsds'],
            [
                'nome' => 'dsds',
                'descricao' => '',
                'preco' => 14.98,
                'duracao' => ,
                'ativo' => true,
            ]
        );

    }
}
