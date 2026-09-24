<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventoParada extends Model
{
    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_EN_CURSO = 'en_curso';
    public const ESTADO_COMPLETADA = 'completada';

    protected $table = 'evento_parada';

    protected $fillable = ['evento_id', 'parada_id', 'hora_estimada', 'orden', 'estado'];

    protected function casts(): array
    {
        return [
            'orden' => 'integer',
        ];
    }

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }

    public function parada(): BelongsTo
    {
        return $this->belongsTo(Parada::class);
    }
}