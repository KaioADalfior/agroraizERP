<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Espelha a aba "Cadastro do Cliente" da planilha: ID, Cliente/Proprietário,
     * Cidade/Estado, Data e Observação. Usa soft delete para nunca perder
     * o histórico de um cliente por engano.
     */
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cidade_estado')->nullable();
            $table->date('data')->nullable();
            $table->text('observacao')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
