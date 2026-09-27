<?php

namespace App\Exceptions;

use App\Models\Evento;
use RuntimeException;

class ReservaNoDisponibleException extends RuntimeException
{
    public static function eventoCerrado(string $estado): self
    {
        return new self(match ($estado) {
            Evento::ESTADO_FINALIZADO => 'Este evento ya se finalizó y no admite nuevas reservas.',
            Evento::ESTADO_CANCELADO => 'Este evento fue cancelado por el guía.',
            default => 'Este evento no admite reservas en su estado actual.',
        });
    }

    public static function eventoLleno(): self
    {
        return new self('Este evento alcanzó su cupo máximo. Elige otra fecha.');
    }

    public static function yaInscrito(): self
    {
        return new self('Ya estás inscrito en este evento.');
    }

    public static function transicionInvalida(string $estadoActual, string $estadoNuevo): self
    {
        return new self("No se puede pasar de «{$estadoActual}» a «{$estadoNuevo}».");
    }
}
