<?php

namespace App\Models;

use Database\Factories\PropriedadeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['nome', 'area_hectares', 'localizacao', 'observacao'])]
class Propriedade extends Model
{
    /** @use HasFactory<PropriedadeFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'area_hectares' => 'decimal:2',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function analisesSolo(): HasMany
    {
        return $this->hasMany(AnaliseSolo::class);
    }
}
