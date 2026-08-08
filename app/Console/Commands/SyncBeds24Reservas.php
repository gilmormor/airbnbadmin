<?php

namespace App\Console\Commands;

use App\Services\Import\Beds24SyncService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('reservas:sync-beds24')]
#[Description('Reconcilia las reservas contra la API de Beds24 (respaldo del webhook en tiempo real).')]
class SyncBeds24Reservas extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(Beds24SyncService $service)
    {
        $importacion = $service->sync();

        $this->info("Sincronización Beds24: {$importacion->total_creadas} creadas, {$importacion->total_actualizadas} actualizadas, {$importacion->total_error} con error.");
    }
}
