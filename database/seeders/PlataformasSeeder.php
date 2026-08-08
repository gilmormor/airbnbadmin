<?php

namespace Database\Seeders;

use App\Models\Plataforma;
use Illuminate\Database\Seeder;

class PlataformasSeeder extends Seeder
{
    public function run(): void
    {
        $plataformas = [
            ['nombre' => 'Airbnb', 'slug' => 'airbnb'],
            ['nombre' => 'Booking.com', 'slug' => 'booking'],
            ['nombre' => 'VRBO', 'slug' => 'vrbo'],
        ];

        foreach ($plataformas as $plataforma) {
            Plataforma::firstOrCreate(['slug' => $plataforma['slug']], $plataforma);
        }
    }
}
