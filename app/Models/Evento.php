<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evento extends Model
{
    use HasFactory;

    public const ESTADO_PROGRAMADO = 'programado';
    public const ESTADO_EN_CURSO = 'en_curso';
    public const ESTADO_FINALIZADO = 'finalizado';
    public const ESTADO_CANCELADO = 'cancelado';

    protected $fillable = ['tour_id', 'guia_id', 'fecha', 'hora_inicio', 'cupo_maximo', 'estado'];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'cupo_maximo' => 'integer',
        ];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function guia(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guia_id');
    }

    public function eventoParadas(): HasMany
    {
        return $this->hasMany(EventoParada::class);
    }

    public function paradas(): BelongsToMany
    {
        return $this->belongsToMany(Parada::class)
            ->using(EventoParada::class)
            ->withPivot('hora_estimada', 'orden', 'estado');
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function participaciones(): HasMany
    {
        return $this->hasMany(Participacion::class);
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(Foto::class);
    }
}