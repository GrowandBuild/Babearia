<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Servico;

class ServicosSeeder extends Seeder
{
    public function run()
    {
        // Serviços sincronizados automaticamente em 12/01/2026 12:23:38
        // Total de serviços: 7

        Servico::updateOrCreate(
            ['nome' => 'Manicure'],
            [
                'nome' => 'Manicure',
                'descricao' => 'Manicure tradicional com esmaltação',
                'preco' => 30.00,
                'duracao' => ,
                'ativo' => true,
            ]
        );

        Servico::updateOrCreate(
            ['nome' => 'Pedicure'],
            [
                'nome' => 'Pedicure',
                'descricao' => 'Pedicure tradicional com esmaltação',
                'preco' => 35.00,
                'duracao' => ,
                'ativo' => true,
            ]
        );

        Servico::updateOrCreate(
            ['nome' => 'Esmaltação em Gel'],
            [
                'nome' => 'Esmaltação em Gel',
                'descricao' => 'Esmaltação em gel com durabilidade de até 3 semanas',
                'preco' => 50.00,
                'duracao' => ,
                'ativo' => true,
            ]
        );

        Servico::updateOrCreate(
            ['nome' => 'Spa dos Pés'],
            [
                'nome' => 'Spa dos Pés',
                'descricao' => 'Tratamento completo dos pés com hidratação e esmaltação',
                'preco' => 60.00,
                'duracao' => ,
                'ativo' => true,
            ]
        );

        Servico::updateOrCreate(
            ['nome' => 'Unhas Decoradas'],
            [
                'nome' => 'Unhas Decoradas',
                'descricao' => 'Esmaltação com decorações e artes especiais',
                'preco' => 70.00,
                'duracao' => ,
                'ativo' => true,
            ]
        );

        Servico::updateOrCreate(
            ['nome' => 'Manicure + Pedicure'],
            [
                'nome' => 'Manicure + Pedicure',
                'descricao' => 'Pacote completo: manicure e pedicure',
                'preco' => 55.00,
                'duracao' => ,
                'ativo' => true,
            ]
        );

        Servico::updateOrCreate(
            ['nome' => 'Corte Masculino'],
            [
                'nome' => 'Corte Masculino',
                'descricao' => 'Corte tradicional masculino',
                'preco' => 30.00,
                'duracao' => ,
                'ativo' => true,
            ]
        );

    }
}
