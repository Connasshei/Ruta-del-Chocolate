<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Foto extends Model
{
    use HasFactory;

    public const TIPO_ACTIVIDAD = 'actividad';
    public const TIPO_LIBRE = 'libre';

    protected $fillable = ['evento_id', 'user_id', 'parada_id', 'ruta_archivo', 'tipo'];

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }

    public function turista(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parada(): BelongsTo
    {
        return $this->belongsTo(Parada::class);
    }
}