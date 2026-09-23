<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('culturas', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            // Teores de nutrientes adequados para alta produtividade (base: Prezotti
            // et al., 2007), extraídos da aba "DataBase" da planilha original.
            $table->decimal('p_resina', 8, 2)->nullable()->comment('P resina, mg/dm³');
            $table->decimal('p_mehlich', 8, 2)->nullable()->comment('P mehlich, mg/dm³');
            $table->decimal('k', 8, 2)->nullable()->comment('K, cmolc/dm³');
            $table->decimal('ca', 8, 2)->nullable()->comment('Ca, cmolc/dm³');
            $table->decimal('mg', 8, 2)->nullable()->comment('Mg, cmolc/dm³');
            $table->decimal('s', 8, 2)->nullable()->comment('S, mg/dm³');
            $table->decimal('b', 8, 2)->nullable()->comment('B, mg/dm³');
            $table->decimal('zn', 8, 2)->nullable()->comment('Zn, mg/dm³');
            $table->decimal('cu', 8, 2)->nullable()->comment('Cu, mg/dm³');
            $table->decimal('fe', 8, 2)->nullable()->comment('Fe, mg/dm³');
            $table->decimal('mn', 8, 2)->nullable()->comment('Mn, mg/dm³');
            $table->timestamps();
        });

        // 48 culturas extraídas da aba "DataBase" (colunas C9:N56) da planilha
        // original "Interpretação de Análise de Solo em Gráficos". Roda junto
        // com a migration (e não como seeder à parte) para já existir em
        // produção assim que o deploy migrar o banco.
        DB::table('culturas')->insert([
            ['nome' => 'Açaí', 'p_resina' => 30, 'p_mehlich' => null, 'k' => 0.3, 'ca' => 4, 'mg' => 1.2, 's' => 10, 'b' => 0.9, 'zn' => 2.2, 'cu' => 1.8, 'fe' => 45, 'mn' => 12],
            ['nome' => 'Algodão', 'p_resina' => 55, 'p_mehlich' => null, 'k' => 0.3, 'ca' => 4, 'mg' => 1.2, 's' => 30, 'b' => 0.5, 'zn' => 1.6, 'cu' => 0.8, 'fe' => 45, 'mn' => 5],
            ['nome' => 'Arroz', 'p_resina' => 30, 'p_mehlich' => 6, 'k' => 0.23, 'ca' => 4, 'mg' => 1, 's' => 10, 'b' => 0.9, 'zn' => 2.2, 'cu' => 1.8, 'fe' => 45, 'mn' => 12],
            ['nome' => 'Arroz Irrigado', 'p_resina' => 30, 'p_mehlich' => 6, 'k' => 0.23, 'ca' => 4, 'mg' => 1, 's' => 10, 'b' => 0.9, 'zn' => 2.2, 'cu' => 1.8, 'fe' => 45, 'mn' => 12],
            ['nome' => 'Banana', 'p_resina' => 30, 'p_mehlich' => 25, 'k' => 0.3, 'ca' => 4, 'mg' => 1.2, 's' => 15, 'b' => 0.9, 'zn' => 2.2, 'cu' => 6, 'fe' => 100, 'mn' => 12],
            ['nome' => 'Café Arábica', 'p_resina' => 30, 'p_mehlich' => null, 'k' => 0.3, 'ca' => 4, 'mg' => 0.9, 's' => 10, 'b' => 1, 'zn' => 10, 'cu' => 5, 'fe' => 30, 'mn' => 6],
            ['nome' => 'Café Conilon', 'p_resina' => 30, 'p_mehlich' => null, 'k' => 0.3, 'ca' => 4, 'mg' => 1, 's' => 10, 'b' => 0.6, 'zn' => 6, 'cu' => 1.5, 'fe' => 30, 'mn' => 15],
            ['nome' => 'Batata', 'p_resina' => 45, 'p_mehlich' => null, 'k' => 0.3, 'ca' => 3, 'mg' => 1.15, 's' => 11, 'b' => 0.6, 'zn' => 1.2, 'cu' => 0.8, 'fe' => 12, 'mn' => 5],
            ['nome' => 'Cana (Planta)', 'p_resina' => 20, 'p_mehlich' => null, 'k' => 0.3, 'ca' => 4, 'mg' => 1.2, 's' => 15, 'b' => 0.6, 'zn' => 1.2, 'cu' => 1, 'fe' => 12, 'mn' => 5],
            ['nome' => 'Cana (Soca)', 'p_resina' => 20, 'p_mehlich' => null, 'k' => 0.3, 'ca' => 4, 'mg' => 1.2, 's' => 15, 'b' => 0.6, 'zn' => 1.2, 'cu' => 1, 'fe' => 12, 'mn' => 5],
            ['nome' => 'Citros', 'p_resina' => 30, 'p_mehlich' => null, 'k' => 0.3, 'ca' => 4, 'mg' => 1.2, 's' => 15, 'b' => 0.8, 'zn' => 4, 'cu' => 1.8, 'fe' => 45, 'mn' => 12],
            ['nome' => 'Erva-mate', 'p_resina' => 28, 'p_mehlich' => null, 'k' => 0.31, 'ca' => 4, 'mg' => 1.2, 's' => 15, 'b' => 0.9, 'zn' => 2.2, 'cu' => 1.8, 'fe' => 45, 'mn' => 12],
            ['nome' => 'Eucalipto', 'p_resina' => 8, 'p_mehlich' => null, 'k' => 0.15, 'ca' => 0.6, 'mg' => 0.4, 's' => 10, 'b' => 0.6, 'zn' => 1, 'cu' => 0.8, 'fe' => 12, 'mn' => 5],
            ['nome' => 'Feijão', 'p_resina' => 35, 'p_mehlich' => null, 'k' => 0.31, 'ca' => 4, 'mg' => 1.2, 's' => 10, 'b' => 0.9, 'zn' => 2.2, 'cu' => 1.8, 'fe' => 45, 'mn' => 12],
            ['nome' => 'Mandioca', 'p_resina' => 25, 'p_mehlich' => null, 'k' => 0.11, 'ca' => 4, 'mg' => 1.2, 's' => 10, 'b' => 0.5, 'zn' => 1.6, 'cu' => 0.8, 'fe' => 45, 'mn' => 5],
            ['nome' => 'Melancia', 'p_resina' => 25, 'p_mehlich' => 25, 'k' => 0.3069, 'ca' => 4, 'mg' => 1.2, 's' => 15, 'b' => 1, 'zn' => 15, 'cu' => 15, 'fe' => 15, 'mn' => 15],
            ['nome' => 'Milho', 'p_resina' => 35, 'p_mehlich' => null, 'k' => 0.3069, 'ca' => 4, 'mg' => 1.2, 's' => 15, 'b' => 1, 'zn' => 3, 'cu' => 0.8, 'fe' => 13, 'mn' => 5],
            ['nome' => 'Milho Silagem', 'p_resina' => 35, 'p_mehlich' => null, 'k' => 0.3069, 'ca' => 4, 'mg' => 1.2, 's' => 15, 'b' => 1, 'zn' => 3, 'cu' => 0.8, 'fe' => 13, 'mn' => 5],
            ['nome' => 'Pastagem', 'p_resina' => 30, 'p_mehlich' => null, 'k' => 0.18, 'ca' => 4, 'mg' => 1, 's' => 10, 'b' => 0.9, 'zn' => 2.2, 'cu' => 1.8, 'fe' => 45, 'mn' => 12],
            ['nome' => 'Sorgo', 'p_resina' => 35, 'p_mehlich' => null, 'k' => 0.31, 'ca' => 4, 'mg' => 1, 's' => 10, 'b' => 0.9, 'zn' => 2.2, 'cu' => 1.8, 'fe' => 45, 'mn' => 12],
            ['nome' => 'Soja', 'p_resina' => 35, 'p_mehlich' => null, 'k' => 0.3069, 'ca' => 4, 'mg' => 1.2, 's' => 15, 'b' => 1, 'zn' => 3, 'cu' => 0.8, 'fe' => 13, 'mn' => 5],
            ['nome' => 'Trigo', 'p_resina' => 30, 'p_mehlich' => null, 'k' => 0.3, 'ca' => 4, 'mg' => 1.2, 's' => 10, 'b' => 0.9, 'zn' => 2.2, 'cu' => 1.8, 'fe' => 45, 'mn' => 12],
            ['nome' => 'Uva', 'p_resina' => 30, 'p_mehlich' => 25, 'k' => 0.35, 'ca' => 4, 'mg' => 1.2, 's' => 15, 'b' => 1, 'zn' => 0.7, 'cu' => 0.3, 'fe' => 45, 'mn' => 15],
            ['nome' => 'Cacau', 'p_resina' => 25, 'p_mehlich' => 17, 'k' => 0.25, 'ca' => 4, 'mg' => 1, 's' => 10, 'b' => 0.9, 'zn' => 2.2, 'cu' => 1.8, 'fe' => 45, 'mn' => 12],
            ['nome' => 'Coco', 'p_resina' => 30, 'p_mehlich' => null, 'k' => 0.3, 'ca' => 4, 'mg' => 1, 's' => 10, 'b' => 1, 'zn' => 5, 'cu' => 1.6, 'fe' => 40, 'mn' => 20],
            ['nome' => 'Hortaliça', 'p_resina' => 30, 'p_mehlich' => null, 'k' => 0.3, 'ca' => 3, 'mg' => 1.15, 's' => 11, 'b' => 0.6, 'zn' => 1.2, 'cu' => 0.8, 'fe' => 12, 'mn' => 5],
            ['nome' => 'Tomate', 'p_resina' => 61, 'p_mehlich' => null, 'k' => 0.3, 'ca' => 4, 'mg' => 1, 's' => 10, 'b' => 0.6, 'zn' => 1.2, 'cu' => 0.8, 'fe' => 41.7, 'mn' => 10],
            ['nome' => 'Morango', 'p_resina' => 61, 'p_mehlich' => null, 'k' => 0.31, 'ca' => 4, 'mg' => 1, 's' => 10, 'b' => 0.2, 'zn' => 0.5, 'cu' => 0.8, 'fe' => 45, 'mn' => 12],
            ['nome' => 'Maracujá', 'p_resina' => 15, 'p_mehlich' => null, 'k' => 0.3, 'ca' => 4, 'mg' => 1, 's' => 10, 'b' => 0.5, 'zn' => 1.6, 'cu' => 0.8, 'fe' => 12, 'mn' => 5],
            ['nome' => 'Abóbora', 'p_resina' => 80, 'p_mehlich' => null, 'k' => 0.5, 'ca' => 4, 'mg' => 1, 's' => 10, 'b' => 0.6, 'zn' => 1.2, 'cu' => 1, 'fe' => 45, 'mn' => 12],
            ['nome' => 'Teca', 'p_resina' => 16, 'p_mehlich' => null, 'k' => 0.15, 'ca' => 4, 'mg' => 1, 's' => 10, 'b' => 0.9, 'zn' => 2.2, 'cu' => 1.8, 'fe' => 45, 'mn' => 12],
            ['nome' => 'Cebola', 'p_resina' => 25, 'p_mehlich' => null, 'k' => 0.25, 'ca' => 4, 'mg' => 1, 's' => 10, 'b' => 0.9, 'zn' => 2.2, 'cu' => 1.8, 'fe' => 45, 'mn' => 12],
            ['nome' => 'Perene', 'p_resina' => 16, 'p_mehlich' => null, 'k' => 0.38, 'ca' => 4, 'mg' => 1, 's' => 10, 'b' => 0.9, 'zn' => 2.2, 'cu' => 1.8, 'fe' => 45, 'mn' => 12],
            ['nome' => 'Amendoim', 'p_resina' => 28, 'p_mehlich' => null, 'k' => 0.3, 'ca' => 3.5, 'mg' => 1.2, 's' => 15, 'b' => 1, 'zn' => 3, 'cu' => 0.8, 'fe' => 13, 'mn' => 5],
            ['nome' => 'Pimenta-do-Reino', 'p_resina' => 30, 'p_mehlich' => null, 'k' => 0.3, 'ca' => 0.8, 'mg' => 1.2, 's' => null, 'b' => 0.9, 'zn' => 2.2, 'cu' => 1.8, 'fe' => 15, 'mn' => 12],
            ['nome' => 'Ornamentais', 'p_resina' => 16, 'p_mehlich' => null, 'k' => 0.38, 'ca' => 4, 'mg' => 1, 's' => 10, 'b' => 0.9, 'zn' => 2.2, 'cu' => 1.8, 'fe' => 45, 'mn' => 12],
            ['nome' => 'Pêssego', 'p_resina' => 35, 'p_mehlich' => null, 'k' => 0.38, 'ca' => 2.5, 'mg' => 1.25, 's' => 15, 'b' => 1, 'zn' => 1.5, 'cu' => 1.25, 'fe' => 10, 'mn' => 20],
            ['nome' => 'Manga', 'p_resina' => 35, 'p_mehlich' => 35, 'k' => 0.35, 'ca' => 4, 'mg' => 1.2, 's' => 15, 'b' => 1, 'zn' => 15, 'cu' => 15, 'fe' => 15, 'mn' => 15],
            ['nome' => 'Caju', 'p_resina' => 35, 'p_mehlich' => 35, 'k' => 0.3069, 'ca' => 4, 'mg' => 1.2, 's' => 10, 'b' => 1, 'zn' => 15, 'cu' => 15, 'fe' => 15, 'mn' => 15],
            ['nome' => 'Graviola', 'p_resina' => 20, 'p_mehlich' => null, 'k' => 0.23, 'ca' => 4, 'mg' => 1, 's' => 10, 'b' => 0.7, 'zn' => 1.8, 'cu' => 1.8, 'fe' => 45, 'mn' => 12],
            ['nome' => 'Mamão', 'p_resina' => 30, 'p_mehlich' => null, 'k' => 0.3, 'ca' => 4, 'mg' => 1.2, 's' => 10, 'b' => 0.6, 'zn' => 4, 'cu' => 1.8, 'fe' => 45, 'mn' => 12],
            ['nome' => 'Seringueira', 'p_resina' => 30, 'p_mehlich' => null, 'k' => 0.3, 'ca' => 4, 'mg' => 1.2, 's' => 10, 'b' => 0.6, 'zn' => 1.2, 'cu' => 0.8, 'fe' => 12, 'mn' => 5],
            ['nome' => 'Pepino', 'p_resina' => 60, 'p_mehlich' => null, 'k' => 0.31, 'ca' => 4, 'mg' => 1.2, 's' => 11, 'b' => 0.6, 'zn' => 1.2, 'cu' => 0.8, 'fe' => 12, 'mn' => 5],
            ['nome' => 'Batata-Doce', 'p_resina' => 40, 'p_mehlich' => null, 'k' => 0.5, 'ca' => 4, 'mg' => 1, 's' => 20, 'b' => 1, 'zn' => 3, 'cu' => 0.5, 'fe' => 5, 'mn' => 5],
            ['nome' => 'Abacaxi', 'p_resina' => 30, 'p_mehlich' => null, 'k' => 0.25, 'ca' => 3, 'mg' => 1, 's' => 10, 'b' => 1.5, 'zn' => 3.25, 'cu' => 1.4, 'fe' => 35, 'mn' => 20],
            ['nome' => 'Tabaco', 'p_resina' => 40, 'p_mehlich' => null, 'k' => 0.3, 'ca' => 4, 'mg' => 1.2, 's' => 10, 'b' => 0.6, 'zn' => 5, 'cu' => 0.8, 'fe' => 12, 'mn' => 1.3],
            ['nome' => 'Goiaba', 'p_resina' => 30, 'p_mehlich' => null, 'k' => 0.65, 'ca' => 4.5, 'mg' => 1.15, 's' => 15, 'b' => 0.75, 'zn' => 4, 'cu' => 0.75, 'fe' => 15, 'mn' => 15],
            ['nome' => 'Abacate', 'p_resina' => 30, 'p_mehlich' => null, 'k' => 0.3, 'ca' => 4, 'mg' => 1, 's' => 10, 'b' => 0.9, 'zn' => 2.2, 'cu' => 1.8, 'fe' => 45, 'mn' => 12],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('culturas');
    }
};
