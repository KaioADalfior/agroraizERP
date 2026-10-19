<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Endereço estruturado da propriedade (pode ser diferente do endereço
        // do cliente — a fazenda raramente fica no mesmo lugar onde o cliente
        // mora). Tudo opcional: propriedades já cadastradas continuam válidas,
        // e o campo livre "localizacao" já existente é preservado para notas
        // que não cabem no endereço estruturado (ex.: "km 12, portão verde").
        Schema::table('propriedades', function (Blueprint $table) {
            $table->string('cep', 9)->nullable()->after('nome');
            $table->string('endereco')->nullable()->after('cep');
            $table->string('numero', 20)->nullable()->after('endereco');
            $table->string('complemento')->nullable()->after('numero');
            $table->string('bairro')->nullable()->after('complemento');
            $table->string('cidade')->nullable()->after('bairro');
            $table->string('estado', 2)->nullable()->after('cidade');
        });
    }

    public function down(): void
    {
        Schema::table('propriedades', function (Blueprint $table) {
            $table->dropColumn(['cep', 'endereco', 'numero', 'complemento', 'bairro', 'cidade', 'estado']);
        });
    }
};
