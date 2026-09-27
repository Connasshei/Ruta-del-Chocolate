<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inscripcion extends Model
{
    use HasFactory;

    protected $table = 'inscripciones';

    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_CONFIRMADO = 'confirmado';
    public const ESTADO_CANCELADO = 'cancelado';

    protected $fillable = ['evento_id', 'user_id', 'estado', 'fecha_inscripcion'];

    protected function casts(): array
    {
        return [
            'fecha_inscripcion' => 'datetime',
        ];
    }

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }

    public function turista(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('estado', '!=', self::ESTADO_CANCELADO);
    }

    public function scopeCanceladas(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_CANCELADO);
    }

    public function estaCancelada(): bool
    {
        return $this->estado === self::ESTADO_CANCELADO;
    }

    public function ocupaCupo(): bool
    {
        return ! $this->estaCancelada();
    }
}