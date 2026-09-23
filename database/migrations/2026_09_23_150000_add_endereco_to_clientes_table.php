<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('cep', 9)->nullable()->after('nome');
            $table->string('endereco')->nullable()->after('cep');
            $table->string('numero', 20)->nullable()->after('endereco');
            $table->string('complemento')->nullable()->after('numero');
            $table->string('bairro')->nullable()->after('complemento');
            $table->string('cidade')->nullable()->after('bairro');
            $table->string('estado', 2)->nullable()->after('cidade');
        });

        // Melhor esforço: aproveita o que dá do texto livre "Cidade-Estado" antigo
        // para preencher os novos campos de cidade/estado antes de remover a coluna.
        DB::table('clientes')
            ->whereNotNull('cidade_estado')
            ->where('cidade_estado', '!=', '')
            ->orderBy('id')
            ->chunkById(100, function ($clientes) {
                foreach ($clientes as $cliente) {
                    $partes = explode('-', (string) $cliente->cidade_estado);
                    $estado = trim((string) array_pop($partes));
                    $cidade = trim(implode('-', $partes));

                    DB::table('clientes')->where('id', $cliente->id)->update([
                        'cidade' => $cidade !== '' ? $cidade : $cliente->cidade_estado,
                        'estado' => strlen($estado) === 2 ? strtoupper($estado) : null,
                    ]);
                }
            });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('cidade_estado');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('cidade_estado')->nullable();
        });

        DB::table('clientes')->orderBy('id')->chunkById(100, function ($clientes) {
            foreach ($clientes as $cliente) {
                $valor = trim(($cliente->cidade ?? '').($cliente->estado ? '-'.$cliente->estado : ''), '-');

                DB::table('clientes')->where('id', $cliente->id)->update([
                    'cidade_estado' => $valor !== '' ? $valor : null,
                ]);
            }
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['cep', 'endereco', 'numero', 'complemento', 'bairro', 'cidade', 'estado']);
        });
    }
};
