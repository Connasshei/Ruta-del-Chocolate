<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recuerdo extends Model
{
    use HasFactory;

    protected $fillable = ['evento_id', 'user_id', 'ruta_archivo', 'datos_generacion'];

    protected function casts(): array
    {
        return [
            'datos_generacion' => 'array',
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
}
