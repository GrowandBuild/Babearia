<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Conta da Proprietária (Val)
        User::updateOrCreate(
            ['email' => 'admin@organizamais.com.br'],
            [
                'name' => 'propietária',
                'email' => 'admin@organizamais.com.br',
                'password' => Hash::make('admin123'),
                'tipo' => 'proprietaria',
                'email_verified_at' => now(),
            ]
        );

        // Conta do Desenvolvedor (Alexandre)
        User::updateOrCreate(
            ['email' => 'alexandre@dev.com'],
            [
                'name' => 'Alexandre Desenvolvedor',
                'email' => 'alexandre@dev.com',
                'password' => Hash::make('dev123'),
                'tipo' => 'proprietaria', // Acesso total para desenvolvimento
                'email_verified_at' => now(),
            ]
        );
        // Apenas dona e desenvolvedor são criados neste seeder conforme solicitado.
    }
}
