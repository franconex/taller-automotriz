<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenTrabajo extends Model
{
    use SoftDeletes;

    protected $table = 'ordenes_trabajo';

    protected $fillable = [
        'numero_orden',
        'cliente_id',
        'vehiculo_id',
        'sucursal_id',
        'usuario_recepcion_id',
        'cita_id',
        'fecha_emision',
        'fecha_inicio',
        'fecha_fin',
        'fecha_entrega',
        'kilometraje_ingreso',
        'descripcion_problema',
        'diagnostico_general',
        'estado',
        'subtotal_servicios',
        'subtotal_repuestos',
        'descuento',
        'total_general',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_emision' => 'datetime',
            'fecha_inicio' => 'datetime',
            'fecha_fin' => 'datetime',
            'fecha_entrega' => 'datetime',
            'subtotal_servicios' => 'decimal:2',
            'subtotal_repuestos' => 'decimal:2',
            'descuento' => 'decimal:2',
            'total_general' => 'decimal:2',
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

    public function usuarioRecepcion(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_recepcion_id');
    }

    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class);
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(AsignacionTrabajo::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleOrdenTrabajo::class);
    }

    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }
}
