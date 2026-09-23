<?php

namespace App\Models;

use Database\Factories\AnaliseSoloFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnaliseSolo extends Model
{
    /** @use HasFactory<AnaliseSoloFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'analises_solo';

    protected function casts(): array
    {
        return [
            'data_coleta' => 'date',
            'ph' => 'decimal:2',
            'materia_organica' => 'decimal:2',
            'p_resina' => 'decimal:2',
            'p_mehlich' => 'decimal:2',
            'k' => 'decimal:2',
            'ca' => 'decimal:2',
            'mg' => 'decimal:2',
            'al' => 'decimal:2',
            'h_al' => 'decimal:2',
            'sb' => 'decimal:2',
            'ctc' => 'decimal:2',
            'v_percentual' => 'decimal:2',
            'm_percentual' => 'decimal:2',
            'areia_percentual' => 'decimal:2',
            'argila_percentual' => 'decimal:2',
            's' => 'decimal:2',
            'b' => 'decimal:2',
            'zn' => 'decimal:2',
            'cu' => 'decimal:2',
            'fe' => 'decimal:2',
            'mn' => 'decimal:2',
        ];
    }

    public function propriedade(): BelongsTo
    {
        return $this->belongsTo(Propriedade::class);
    }

    public function cultura(): BelongsTo
    {
        return $this->belongsTo(Cultura::class);
    }
}
