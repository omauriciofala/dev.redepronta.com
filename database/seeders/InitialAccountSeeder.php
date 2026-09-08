<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class InitialAccountSeeder extends Seeder
{
    public function run(): void
    {
        $account = Account::firstOrCreate(
            ['subdomain' => 'matriz'],
            [
                'name' => 'Rede Pronta Telecom Matriz',
                'subdomain' => 'matriz',
                'document' => '12345678000190',
                'email' => 'admin@redepronta.com',
                'phone' => '(11) 3000-0000',
                'status' => 'active',
                'settings' => [
                    'theme' => 'dark',
                    'primary_color' => '#2563eb',
                ],
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@redepronta.com'],
            [
                'account_id' => $account->id,
                'name' => 'Administrador Matriz',
                'email' => 'admin@redepronta.com',
                'password' => Hash::make('RedePronta@2026'),
            ]
        );
    }
}
