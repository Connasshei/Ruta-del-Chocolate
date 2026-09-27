<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RutaTipo extends Model
{
    use HasFactory;

    protected $table = 'ruta_tipos';

    protected $fillable = [
        'tour_id',
        'nombre',
        'descripcion',
        'duracion_minutos',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'duracion_minutos' => 'integer',
            'activo' => 'boolean',
        ];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function paradas(): HasMany
    {
        return $this->hasMany(RutaTipoParada::class);
    }

    public function paradasOrdenadas()
    {
        return $this->paradas()->orderBy('orden')->get();
    }

    /**
     * Obtiene todas las paradas de esta ruta tipo con su información
     */
    public function paradasConDetalles()
    {
        return $this->paradas()
            ->with('parada')
            ->orderBy('orden')
            ->get()
            ->map(function ($item) {
                return [
                    'parada_id' => $item->parada_id,
                    'parada' => $item->parada,
                    'orden' => $item->orden,
                    'duracion_minutos' => $item->duracion_minutos,
                    'actividades_ids' => $item->actividades_ids,
                ];
            });
    }
}
