<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RutaTipoParada extends Model
{
    use HasFactory;

    protected $table = 'ruta_tipo_parada';

    protected $fillable = [
        'ruta_tipo_id',
        'parada_id',
        'orden',
        'duracion_minutos',
        'actividades_ids',
    ];

    protected function casts(): array
    {
        return [
            'orden' => 'integer',
            'duracion_minutos' => 'integer',
            'actividades_ids' => 'array',
        ];
    }

    public function rutaTipo(): BelongsTo
    {
        return $this->belongsTo(RutaTipo::class);
    }

    public function parada(): BelongsTo
    {
        return $this->belongsTo(Parada::class);
    }
}
