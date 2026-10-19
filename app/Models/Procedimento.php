<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['nome', 'banco_nome', 'banco_codigo', 'tipo_operacao', 'fase_proposta', 'porcentagem_cobrada', 'pagamento_recebido'])]
class Procedimento extends Model
{
    use SoftDeletes;

    /**
     * Valores aceitos para "tipo de operação". Lista fechada por design do negócio
     * (ao contrário de "fase_proposta", que é livre).
     *
     * @var array<int, string>
     */
    public const TIPOS_OPERACAO = ['Custeio', 'Investimento'];

    protected function casts(): array
    {
        return [
            'banco_codigo' => 'integer',
            'porcentagem_cobrada' => 'decimal:2',
            'pagamento_recebido' => 'boolean',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
