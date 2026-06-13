<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega la columna updated_by (quién hizo la última actualización) a configuracion.
     */
    public function up(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->unsignedBigInteger('updated_by')->nullable()->after('valor');
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
            $table->dropColumn('updated_by');
        });
    }
};
