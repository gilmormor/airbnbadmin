<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'gilmormor@gmail.com'],
            [
                'name' => 'Administrador',
                'password' => 'CambiarPassword123!',
            ]
        );

        if (! $admin->hasRole('Administrador')) {
            $admin->assignRole('Administrador');
        }
    }
}
