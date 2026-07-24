<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'clave',
        'valor',
        'tipo',
        'grupo',
        'descripcion',
    ];

    public static function obtener(string $clave, mixed $porDefecto = null): mixed
    {
        $cacheKey = 'setting:' . $clave;

        return Cache::rememberForever($cacheKey, function () use ($clave, $porDefecto) {
            $registro = static::where('clave', $clave)->first();

            if (! $registro) {
                return $porDefecto;
            }

            return match ($registro->tipo) {
                'booleano' => (bool) $registro->valor,
                'numero' => is_numeric($registro->valor) ? $registro->valor + 0 : $porDefecto,
                'json' => json_decode($registro->valor ?? '[]', true) ?? $porDefecto,
                default => $registro->valor ?? $porDefecto,
            };
        });
    }

    public static function guardar(string $clave, mixed $valor, string $tipo = 'texto', string $grupo = 'general', ?string $descripcion = null): void
    {
        $valorPersistir = match ($tipo) {
            'booleano' => $valor ? '1' : '0',
            'json' => json_encode($valor),
            default => (string) $valor,
        };

        static::updateOrCreate(
            ['clave' => $clave],
            [
                'valor' => $valorPersistir,
                'tipo' => $tipo,
                'grupo' => $grupo,
                'descripcion' => $descripcion,
            ]
        );

        Cache::forget('setting:' . $clave);
    }
}
