<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analises_solo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propriedade_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cultura_id')->nullable()->constrained()->nullOnDelete();
            $table->string('profundidade')->nullable();
            $table->date('data_coleta')->nullable();

            // Valores de laboratório (mesmas unidades/colunas da aba "Inserir
            // Dados da Análise" da planilha original).
            $table->decimal('ph', 5, 2)->nullable()->comment('pH em CaCl2');
            $table->decimal('materia_organica', 6, 2)->nullable()->comment('M.O., %');
            $table->decimal('p_resina', 8, 2)->nullable()->comment('P resina, mg/dm³');
            $table->decimal('p_mehlich', 8, 2)->nullable()->comment('P mehlich, mg/dm³');
            $table->decimal('k', 8, 2)->nullable()->comment('K, cmolc/dm³');
            $table->decimal('ca', 8, 2)->nullable()->comment('Ca, cmolc/dm³');
            $table->decimal('mg', 8, 2)->nullable()->comment('Mg, cmolc/dm³');
            $table->decimal('al', 8, 2)->nullable()->comment('Al, cmolc/dm³');
            $table->decimal('h_al', 8, 2)->nullable()->comment('H+Al, cmolc/dm³');
            $table->decimal('sb', 8, 2)->nullable()->comment('Soma de bases, cmolc/dm³');
            $table->decimal('ctc', 8, 2)->nullable()->comment('CTC (T), cmolc/dm³');
            $table->decimal('v_percentual', 6, 2)->nullable()->comment('V%, saturação por bases');
            $table->decimal('m_percentual', 6, 2)->nullable()->comment('m%, saturação por alumínio');
            $table->decimal('areia_percentual', 6, 2)->nullable();
            $table->decimal('argila_percentual', 6, 2)->nullable();
            $table->decimal('s', 8, 2)->nullable()->comment('S, mg/dm³');
            $table->decimal('b', 8, 2)->nullable()->comment('B, mg/dm³');
            $table->decimal('zn', 8, 2)->nullable()->comment('Zn, mg/dm³');
            $table->decimal('cu', 8, 2)->nullable()->comment('Cu, mg/dm³');
            $table->decimal('fe', 8, 2)->nullable()->comment('Fe, mg/dm³');
            $table->decimal('mn', 8, 2)->nullable()->comment('Mn, mg/dm³');

            $table->text('observacao')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analises_solo');
    }
};
