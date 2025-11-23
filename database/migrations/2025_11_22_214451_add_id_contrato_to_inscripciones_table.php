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
        Schema::table('inscripcion', function (Blueprint $table) {
            $table->unsignedBigInteger('id_contrato')->nullable()->after('id');

            $table->foreign('id_contrato')
                ->references('id')->on('contratos')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscripcion', function (Blueprint $table) {
            $table->dropForeign(['id_contrato']);
            $table->dropColumn('id_contrato');
        });
    }
};
