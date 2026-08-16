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
            // Canal propio: reservas hechas desde el sitio web, sin comisión de OTA.
            ['nombre' => 'Directo', 'slug' => 'directo'],
        ];

        foreach ($plataformas as $plataforma) {
            Plataforma::firstOrCreate(['slug' => $plataforma['slug']], $plataforma);
        }
    }
}
