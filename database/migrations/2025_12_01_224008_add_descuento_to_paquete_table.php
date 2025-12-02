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
        Schema::table('paquete', function (Blueprint $table) {
            $table->decimal('descuento', 5, 2)->default(0)->after('costo_paquete');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paquete', function (Blueprint $table) {
            $table->dropColumn('descuento');
        });
    }
};
