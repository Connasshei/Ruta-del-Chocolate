<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participacion extends Model
{
    use HasFactory;

    protected $table = 'participaciones';

    protected $fillable = ['evento_id', 'actividad_id', 'user_id', 'puntaje', 'intentos', 'completado_en'];

    protected function casts(): array
    {
        return [
            'puntaje' => 'integer',
            'intentos' => 'integer',
            'completado_en' => 'datetime',
        ];
    }

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class);
    }

    public function turista(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}