<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procedimentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->cascadeOnDelete();
            $table->string('nome');
            $table->string('banco_nome')->nullable();
            $table->unsignedSmallInteger('banco_codigo')->nullable();
            $table->string('tipo_operacao');
            $table->string('fase_proposta')->nullable();
            $table->decimal('porcentagem_cobrada', 5, 2)->nullable();
            $table->boolean('pagamento_recebido')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procedimentos');
    }
};
