<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacion', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 50);
            $table->json('mensaje');
            $table->string('entidad_tipo', 50)->nullable();
            $table->unsignedBigInteger('entidad_id')->nullable();
            $table->string('modo', 20);
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->timestamp('resuelta_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
            $table->index(['tipo', 'entidad_tipo', 'entidad_id'], 'notif_entidad_idx');
            $table->index(['resuelta_at', 'fecha_inicio', 'fecha_fin'], 'notif_vigencia_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacion');
    }
};
