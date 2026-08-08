<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_id')->constrained('departamentos')->restrictOnDelete();
            $table->foreignId('plataforma_id')->constrained('plataformas')->restrictOnDelete();
            $table->string('codigo_externo');
            $table->string('huesped')->nullable();
            $table->date('fecha_checkin');
            $table->date('fecha_checkout');
            $table->unsignedSmallInteger('noches')->nullable();
            $table->date('fecha_reserva')->nullable();
            $table->string('estado')->default('confirmada');
            $table->decimal('monto_bruto', 10, 2)->default(0);
            $table->decimal('comision_plataforma', 10, 2)->default(0);
            $table->decimal('comision_coanfitrion', 10, 2)->default(0);
            $table->decimal('ingreso_liquido_propietario', 10, 2)->default(0);
            $table->string('moneda', 3)->default('USD');
            $table->string('origen');
            $table->json('payload_origen')->nullable();
            $table->timestamps();

            $table->unique(['plataforma_id', 'codigo_externo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
