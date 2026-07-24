<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cita extends Model
{
    use SoftDeletes;

    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'confirmada' => 'Confirmada',
        'atendida' => 'Atendida',
        'cancelada' => 'Cancelada',
        'no_asistio' => 'No asistió',
    ];

    public const COLORES = [
        'confirmada' => '#16A34A',
        'pendiente'  => '#F59E0B',
        'atendida'   => '#0891B2',
        'cancelada'  => '#9CA3AF',
        'no_asistio' => '#B91C1C',
    ];

    protected $fillable = [
        'cliente_id',
        'vehiculo_id',
        'sucursal_id',
        'usuario_id',
        'servicio_id',
        'mecanico_id',
        'fecha',
        'hora',
        'hora_fin',
        'duracion_minutos',
        'tipo',
        'descripcion_problema',
        'estado',
        'estado_anterior',
        'deja_vehiculo',
        'costo_consulta',
        'observaciones',
        'motivo_reprogramacion',
        'reprogramado_por_id',
        'reprogramado_en',
        'cancelado_motivo',
        'cancelado_por_id',
        'cancelado_en',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'hora' => 'string',
            'hora_fin' => 'string',
            'deja_vehiculo' => 'boolean',
            'costo_consulta' => 'decimal:2',
            'reprogramado_en' => 'datetime',
            'cancelado_en' => 'datetime',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }

    public function mecanico(): BelongsTo
    {
        return $this->belongsTo(Mecanico::class);
    }

    public function reprogramadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reprogramado_por_id');
    }

    public function canceladoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelado_por_id');
    }

    public function ordenTrabajo()
    {
        return $this->hasOne(OrdenTrabajo::class);
    }

    /* ===========================
       Scopes
       =========================== */

    public function scopeEnRango(Builder $query, string $inicio, string $fin): Builder
    {
        return $query->whereBetween('fecha', [$inicio, $fin]);
    }

    public function scopeNoCancelada(Builder $query): Builder
    {
        return $query->where('estado', '!=', 'cancelada');
    }

    public function scopeFuturas(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('fecha', '>=', now()->toDateString());
        })->orderBy('fecha')->orderBy('hora');
    }

    public function scopeDeFecha(Builder $query, string $fecha): Builder
    {
        return $query->whereBetween('fecha', [$fecha . ' 00:00:00', $fecha . ' 23:59:59']);
    }

    /* ===========================
       Accessors / Helpers
       =========================== */

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? ucfirst((string) $this->estado);
    }

    public function getEstadoColorAttribute(): string
    {
        return self::COLORES[$this->estado] ?? '#6B7280';
    }

    public function getEstadoBadgeClassAttribute(): string
    {
        return match ($this->estado) {
            'confirmada' => 'bg-success-subtle text-success-emphasis border-success-subtle',
            'pendiente'  => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
            'atendida'   => 'bg-info-subtle text-info-emphasis border-info-subtle',
            'cancelada'  => 'bg-secondary-subtle text-secondary-emphasis border-secondary-subtle',
            'no_asistio' => 'bg-danger-subtle text-danger-emphasis border-danger-subtle',
            default      => 'bg-light text-dark border',
        };
    }

    /**
     * Calcula la hora de fin estimada (default 1h) si no está definida.
     */
    public function horaFinCalculada(): string
    {
        if ($this->hora_fin) {
            return $this->hora_fin;
        }

        $inicio = \Carbon\Carbon::createFromFormat('H:i:s', $this->hora . ':00');
        $fin = $inicio->copy()->addMinutes($this->duracion_minutos ?: 60);

        return $fin->format('H:i');
    }

    public function yaPaso(): bool
    {
        $fin = $this->hora_fin ?: $this->hora;
        $finCompleta = $this->fecha?->format('Y-m-d') . ' ' . $fin;

        return $finCompleta ? now()->greaterThan($finCompleta) : false;
    }

    public function estaCancelada(): bool
    {
        return $this->estado === 'cancelada';
    }

    public function estaAtendida(): bool
    {
        return $this->estado === 'atendida';
    }

    public function esPasableReprogramar(): bool
    {
        return ! in_array($this->estado, ['cancelada', 'atendida', 'no_asistio'], true);
    }

    public function esPasableConfirmar(): bool
    {
        return $this->estado === 'pendiente';
    }

    public function esPasableCancelar(): bool
    {
        return ! in_array($this->estado, ['cancelada', 'atendida', 'no_asistio'], true);
    }

    public function esPasableNoAsistio(): bool
    {
        if (! in_array($this->estado, ['pendiente', 'confirmada'], true)) {
            return false;
        }

        return $this->yaPaso();
    }
}
