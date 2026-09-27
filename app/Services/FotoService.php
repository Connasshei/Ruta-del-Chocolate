<?php

namespace App\Services;

use App\Models\Evento;
use App\Models\Foto;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FotoService
{
    private const DISK = 'fotos';
    private const MAX_SIZE = 5 * 1024 * 1024; // 5MB
    private const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

    public function subirFoto(
        Evento $evento,
        User $turista,
        UploadedFile $archivo,
        ?int $paradaId = null,
        string $tipo = Foto::TIPO_LIBRE
    ): Foto {
        $this->validarArchivo($archivo);

        $ruta = $this->guardarArchivo($evento, $turista, $archivo);

        return Foto::create([
            'evento_id' => $evento->id,
            'user_id' => $turista->id,
            'parada_id' => $paradaId,
            'ruta_archivo' => $ruta,
            'tipo' => $tipo,
        ]);
    }

    public function eliminarFoto(Foto $foto): bool
    {
        Storage::disk(self::DISK)->delete($foto->ruta_archivo);
        return $foto->delete();
    }

    public function limpiarEventoFotos(Evento $evento): void
    {
        $fotos = Foto::where('evento_id', $evento->id)->get();

        foreach ($fotos as $foto) {
            Storage::disk(self::DISK)->delete($foto->ruta_archivo);
            $foto->delete();
        }
    }

    public function obtenerFotosEvento(Evento $evento)
    {
        return Foto::where('evento_id', $evento->id)
            ->orderByDesc('created_at')
            ->get();
    }

    public function obtenerFotosUsuario(Evento $evento, User $turista)
    {
        return Foto::where('evento_id', $evento->id)
            ->where('user_id', $turista->id)
            ->orderByDesc('created_at')
            ->get();
    }

    public function obtenerUrlFoto(Foto $foto): string
    {
        return Storage::disk(self::DISK)->url($foto->ruta_archivo);
    }

    private function validarArchivo(UploadedFile $archivo): void
    {
        if ($archivo->getSize() > self::MAX_SIZE) {
            throw new \InvalidArgumentException('La imagen no debe superar 5MB.');
        }

        if (! in_array($archivo->getMimeType(), self::ALLOWED_TYPES)) {
            throw new \InvalidArgumentException('Solo se permiten archivos JPG, PNG o WebP.');
        }
    }

    private function guardarArchivo(Evento $evento, User $turista, UploadedFile $archivo): string
    {
        $nombre = "{$turista->id}_{time()}_{uniqid()}.{$archivo->getClientOriginalExtension()}";
        $ruta = "evento_{$evento->id}/{$turista->id}";

        return $archivo->storeAs($ruta, $nombre, self::DISK);
    }
}
