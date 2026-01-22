<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('certificado', function (Blueprint $table) {
            $table->foreignId('plantilla_certificado_id')
                ->nullable()
                ->constrained('plantillas_certificados')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('certificado', function (Blueprint $table) {
            $table->dropForeign(['plantilla_certificado_id']);
            $table->dropColumn('plantilla_certificado_id');
        });
    }
};
