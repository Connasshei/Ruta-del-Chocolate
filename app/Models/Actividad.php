<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Actividad extends Model
{
    use HasFactory;

    protected $table = 'actividades';

    public const TIPO_TRIVIA = 'trivia';
    public const TIPO_QUIZ_FOTO = 'quiz_foto';
    public const TIPO_ENCUENTRA_DIFERENCIA = 'encuentra_diferencia';
    public const TIPO_OTRO = 'otro';

    protected $fillable = ['parada_id', 'nombre', 'tipo', 'configuracion', 'puntos_max', 'activo'];

    protected function casts(): array
    {
        return [
            'configuracion' => 'array',
            'puntos_max' => 'integer',
            'activo' => 'boolean',
        ];
    }

    public function parada(): BelongsTo
    {
        return $this->belongsTo(Parada::class);
    }

    public function participaciones(): HasMany
    {
        return $this->hasMany(Participacion::class);
    }
}