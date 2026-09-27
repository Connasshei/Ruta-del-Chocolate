<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    public const ESTADOS = [
        self::ESTADO_PROGRAMADO,
        self::ESTADO_EN_CURSO,
        self::ESTADO_FINALIZADO,
        self::ESTADO_CANCELADO,
    ];

    public const TIPO_RUTA_COMPLETA = 'completa';
    public const TIPO_RUTA_RAPIDA = 'rapida';
    public const TIPO_RUTA_PERSONALIZADA = 'personalizada';

    public const TIPOS_RUTA = [
        self::TIPO_RUTA_COMPLETA,
        self::TIPO_RUTA_RAPIDA,
        self::TIPO_RUTA_PERSONALIZADA,
    ];

    /**
     * Estados a los que se puede llevar un evento desde cada estado actual.
     */
    public const TRANSICIONES = [
        self::ESTADO_PROGRAMADO => [self::ESTADO_EN_CURSO, self::ESTADO_CANCELADO],
        self::ESTADO_EN_CURSO => [self::ESTADO_FINALIZADO, self::ESTADO_CANCELADO],
        self::ESTADO_FINALIZADO => [],
        self::ESTADO_CANCELADO => [self::ESTADO_PROGRAMADO],
    ];

    protected $fillable = [
        'tour_id', 'guia_id', 'fecha', 'hora_inicio', 'cupo_maximo', 'estado',
        'tipo_ruta', 'duracion_total_minutos',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'cupo_maximo' => 'integer',
            'duracion_total_minutos' => 'integer',
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

    public function scopeDeGuia(Builder $query, User $guia): Builder
    {
        return $query->where('guia_id', $guia->id);
    }

    public function scopeConEstado(Builder $query, ?string $estado): Builder
    {
        return $estado ? $query->where('estado', $estado) : $query;
    }

    public function scopeAbiertosALasInscripciones(Builder $query): Builder
    {
        return $query->whereIn('estado', [self::ESTADO_PROGRAMADO, self::ESTADO_EN_CURSO]);
    }

    public function scopeProximos(Builder $query): Builder
    {
        return $query->orderBy('fecha')->orderBy('hora_inicio');
    }

    public function scopeRecientes(Builder $query): Builder
    {
        return $query->orderByDesc('fecha')->orderByDesc('hora_inicio');
    }

    public function scopeVisiblesPara(Builder $query, User $usuario): Builder
    {
        return $usuario->hasAnyRole(['guia', 'admin'])
            ? $query
            : $query->abiertosALasInscripciones();
    }

    public function inscripcionesActivas(): HasMany
    {
        return $this->inscripciones()->where('estado', '!=', Inscripcion::ESTADO_CANCELADO);
    }

    public function cuposRestantes(): int
    {
        return max(0, $this->cupo_maximo - $this->inscripcionesActivas()->count());
    }

    public function estaLleno(): bool
    {
        return $this->cuposRestantes() === 0;
    }

    public function admiteInscripciones(): bool
    {
        return in_array($this->estado, [self::ESTADO_PROGRAMADO, self::ESTADO_EN_CURSO], true)
            && ! $this->estaLleno();
    }

    public function inscripcionDe(User $usuario): ?Inscripcion
    {
        return $this->inscripciones()->where('user_id', $usuario->id)->first();
    }

    public function estaInscrito(User $usuario): bool
    {
        return $this->inscripcionDe($usuario)?->estado === Inscripcion::ESTADO_CONFIRMADO;
    }

    public function puedeTransicionarA(string $estado): bool
    {
        return in_array($estado, self::TRANSICIONES[$this->estado] ?? [], true);
    }

    public function horaInicio(): string
    {
        return $this->hora_inicio ? substr((string) $this->hora_inicio, 0, 5) : '';
    }

    public function rutaOrdenada()
    {
        return $this->paradas()->orderBy('evento_parada.orden')->get();
    }

    public function scopePorTipoRuta(Builder $query, string $tipo): Builder
    {
        return $query->where('tipo_ruta', $tipo);
    }

    /**
     * Obtiene la hora estimada de finalización del evento
     */
    public function horaFinalizacion()
    {
        $horaInicio = \Carbon\Carbon::createFromFormat('H:i:s', (string) $this->hora_inicio);
        return $horaInicio->addMinutes($this->duracion_total_minutos)->format('H:i');
    }

    /**
     * Calcula la duración total basada en las paradas
     */
    public function calcularDuracionTotal(): int
    {
        return (int) $this->eventoParadas()
            ->sum('duracion_minutos') ?? $this->duracion_total_minutos;
    }
}