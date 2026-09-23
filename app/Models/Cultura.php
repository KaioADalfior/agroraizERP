<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Referência de teores de nutrientes adequados por cultura, para alta
 * produtividade (base: Prezotti et al., 2007). Dado de referência: não é
 * criado nem editado pela aplicação, só semeado pela migration a partir da
 * planilha original.
 */
class Cultura extends Model
{
    protected function casts(): array
    {
        return [
            'p_resina' => 'decimal:2',
            'p_mehlich' => 'decimal:2',
            'k' => 'decimal:2',
            'ca' => 'decimal:2',
            'mg' => 'decimal:2',
            's' => 'decimal:2',
            'b' => 'decimal:2',
            'zn' => 'decimal:2',
            'cu' => 'decimal:2',
            'fe' => 'decimal:2',
            'mn' => 'decimal:2',
        ];
    }

    public function analisesSolo(): HasMany
    {
        return $this->hasMany(AnaliseSolo::class);
    }
}
