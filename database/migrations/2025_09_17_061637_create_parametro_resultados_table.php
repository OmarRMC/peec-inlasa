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
        Schema::create('parametro_campos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_parametro');   // Relación al parámetro
            $table->unsignedBigInteger('id_grupo_selector')->nullable(); // Opciones select

            $table->string('nombre');
            $table->string('label')->nullable();
            $table->enum('tipo', [
                'text',
                'number',
                'date',
                'select',
                'checkbox',
                'textarea',
                'email',
                'file',
                'datalist',
                'radio'
            ]);

            // Configuración UI
            $table->string('placeholder')->nullable();
            $table->string('unidad', 50)->nullable();     // Ej: kg, m, °C
            $table->integer('posicion')->default(0);      // Orden
            $table->string('mensaje')->nullable();

            // Validaciones básicas
            $table->boolean('requerido')->default(false);
            $table->integer('min')->nullable();           // Para number/date/length
            $table->integer('max')->nullable();
            $table->integer('minlength')->nullable();
            $table->integer('maxlength')->nullable();
            $table->string('step')->nullable();           // Para number/decimal

            // Validaciones avanzadas
            $table->string('pattern')->nullable();        // Expresión regular
            $table->json('reglas')->nullable();           // Validaciones extra en JSON
            $table->boolean('modificable')->default(true);

            // Dependencias
            $table->json('dependencias')->nullable();     // Ej: mostrar si otro input = X

            // Rango específico
            $table->json('rangeNumber')->nullable();      // valores predefinidos para numeric
            $table->json('rangeLength')->nullable();      // longitudes permitidas

            // Relaciones
            $table->foreign('id_parametro')
                ->references('id')->on('parametros')
                ->onDelete('cascade');

            $table->foreign('id_grupo_selector')
                ->references('id')->on('grupos_selectores')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parametro_campos');
    }
};
