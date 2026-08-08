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
        Schema::table('departamentos', function (Blueprint $table) {
            $table->unsignedInteger('beds24_prop_id')->nullable()->after('comision_coanfitrion_pct');
            $table->unsignedInteger('beds24_room_id')->nullable()->after('beds24_prop_id');
            $table->unique(['beds24_prop_id', 'beds24_room_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departamentos', function (Blueprint $table) {
            $table->dropUnique(['beds24_prop_id', 'beds24_room_id']);
            $table->dropColumn(['beds24_prop_id', 'beds24_room_id']);
        });
    }
};
