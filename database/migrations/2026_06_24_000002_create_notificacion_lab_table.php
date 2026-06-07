<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacion_lab', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_notificacion');
            $table->unsignedBigInteger('id_lab');
            $table->timestamp('leido_en')->nullable();
            $table->timestamp('resuelta_en')->nullable();
            $table->timestamps();

            $table->foreign('id_notificacion')->references('id')->on('notificacion')->cascadeOnDelete();
            $table->foreign('id_lab')->references('id')->on('laboratorio');
            $table->unique(['id_notificacion', 'id_lab'], 'notif_lab_unico');
            $table->index(['id_lab', 'leido_en', 'resuelta_en'], 'notif_lab_pendientes_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacion_lab');
    }
};
