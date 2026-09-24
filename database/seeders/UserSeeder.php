<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@rutachocolate.test'],
            [
                'name' => 'Administrador',
                'password' => 'password',
                'consentimiento_marketing' => true,
            ]
        );
        $admin->assignRole('admin');

        $guias = [
            ['name' => 'Guía María', 'email' => 'guia@rutachocolate.test'],
            ['name' => 'Guía Carlos', 'email' => 'guia2@rutachocolate.test'],
        ];

        foreach ($guias as $guia) {
            $user = User::firstOrCreate(
                ['email' => $guia['email']],
                ['name' => $guia['name'], 'password' => 'password']
            );
            $user->assignRole('guia');
        }

        $turistas = [
            ['name' => 'Turista Ana', 'email' => 'turista@rutachocolate.test', 'ciudad_origen' => 'La Paz'],
            ['name' => 'Turista Luis', 'email' => 'turista2@rutachocolate.test', 'ciudad_origen' => 'Cochabamba'],
        ];

        foreach ($turistas as $turista) {
            $user = User::firstOrCreate(
                ['email' => $turista['email']],
                [
                    'name' => $turista['name'],
                    'password' => 'password',
                    'ciudad_origen' => $turista['ciudad_origen'],
                ]
            );
            $user->assignRole('turista');
        }
    }
}