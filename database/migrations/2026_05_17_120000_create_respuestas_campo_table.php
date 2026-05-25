<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('respuestas_campo', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_formulario_resultado')
                ->constrained('formulario_ensayos_resultados')->cascadeOnDelete();
            $table->foreignId('id_parametro_campo')
                ->constrained('parametro_campos')->restrictOnDelete();

            $table->enum('tipo_campo', [
                'text','number','date','select','checkbox',
                'textarea','email','file','datalist','radio',
            ]);

            $table->string('nombre_parametro', 500)->nullable();
            $table->string('valor_texto', 500)->nullable();
            $table->decimal('valor_numero', 20, 6)->nullable();
            $table->date('valor_fecha')->nullable();
            $table->boolean('valor_booleano')->nullable();
            $table->foreignId('id_opcion_seleccionada')
                ->nullable()->constrained('opciones_selector')->nullOnDelete();
            $table->string('valor_texto_opcion', 500)->nullable();

            $table->timestamps();

            $table->unique(['id_formulario_resultado', 'id_parametro_campo'], 'uq_resultado_campo');
            $table->index(['id_parametro_campo', 'valor_numero'], 'idx_campo_numero');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('respuestas_campo');
    }
};
