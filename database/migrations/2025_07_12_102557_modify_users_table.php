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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id_cargo')->nullable();
            $table->string('username', 50);
            $table->string('nombre', 50);
            $table->string('ap_paterno', 50);
            $table->string('ap_materno', 50)->nullable();
            $table->string('ci', 15);
            $table->string('telefono', 20)->nullable(); // Se asume que 'talefono' es un error de tipeo de 'telefono'
            $table->boolean('status');
            $table->foreign('id_cargo')->references('id')->on('cargo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_cargo']);
            $table->dropColumn([
                'id_usuario',
                'id_cargo',
                'login',
                'nombre',
                'ap_paterno',
                'ap_materno',
                'ci',
                'talefono',
                'correo',
                'password',
                'status_usuario'
            ]);
        });
    }
};
