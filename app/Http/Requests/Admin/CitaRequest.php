<?php

namespace App\Http\Requests\Admin;

use App\Models\Cita;
use App\Models\Mecanico;
use App\Models\Sucursal;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;

class CitaRequest extends AdminFormRequest
{
    public function rules(): array
    {
        return [
            'cliente_id' => ['required', 'exists:clientes,id'],
            'vehiculo_id' => [
                'required',
                'exists:vehiculos,id',
                function ($attribute, $value, $fail) {
                    $vehiculo = Vehiculo::find($value);
                    if (! $vehiculo) return;
                    if ((int) $vehiculo->cliente_id !== (int) $this->input('cliente_id')) {
                        $fail('El vehículo seleccionado no pertenece al cliente indicado.');
                    }
                },
            ],
            'sucursal_id' => [
                'required',
                'exists:sucursales,id',
                function ($attribute, $value, $fail) {
                    $sucursal = Sucursal::find($value);
                    if ($sucursal && ! $sucursal->estado) {
                        $fail('La sucursal seleccionada está inactiva.');
                    }
                },
            ],
            'servicio_id' => ['nullable', 'exists:servicios,id'],
            'mecanico_id' => [
                'nullable',
                'exists:mecanicos,id',
                function ($attribute, $value, $fail) {
                    $mecanico = Mecanico::find($value);
                    if (! $mecanico) return;
                    $empleado = $mecanico->empleado;
                    if (! $empleado || ! $empleado->estado) {
                        $fail('El mecánico seleccionado no está activo.');
                    }
                    if ($mecanico->disponibilidad !== 'disponible') {
                        $fail('El mecánico seleccionado no está disponible en este momento.');
                    }
                },
            ],
            'fecha' => ['required', 'date'],
            'hora' => ['required', 'date_format:H:i'],
            'tipo' => ['required', 'in:diagnostico,mantenimiento,reparacion,otro'],
            'descripcion_problema' => ['nullable', 'string', 'max:1000'],
            'costo_consulta' => ['nullable', 'numeric', 'min:0'],
            'deja_vehiculo' => ['nullable', 'boolean'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
            'estado' => ['nullable', 'in:pendiente,confirmada,atendida,cancelada,no_asistio'],
            'motivo_reprogramacion' => [new RequiredIf($this->input('__accion') === 'reprogramar'), 'nullable', 'string', 'max:1000'],
            'cancelado_motivo' => [new RequiredIf($this->input('__accion') === 'cancelar'), 'nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) return;

            $fecha  = $this->input('fecha');
            $hora   = $this->input('hora');
            $horaFin = $this->calcularHoraFinPorDefecto($hora);
            $vehiculoId = $this->input('vehiculo_id');
            $mecanicoId = $this->input('mecanico_id');
            $citaId = $this->route('cita')?->id;

            if (! $fecha || ! $hora || ! $horaFin) return;

            try {
                $inicio = Carbon::createFromFormat('Y-m-d H:i', $fecha . ' ' . $hora);
                $fin    = Carbon::createFromFormat('Y-m-d H:i', $fecha . ' ' . $horaFin);
            } catch (\Throwable $e) {
                return;
            }

            if ($this->isMethod('POST') && $inicio->lessThan(now()->subMinutes(5))) {
                $validator->errors()->add('fecha', 'No puedes agendar una cita en el pasado.');
                return;
            }

            // Choque del mismo vehículo
            $choqueVehiculo = Cita::query()
                ->where('vehiculo_id', $vehiculoId)
                ->where('estado', '!=', 'cancelada')
                ->whereRaw('DATE(fecha) = ?', [$fecha])
                ->when($citaId, fn ($q) => $q->where('id', '!=', $citaId))
                ->get()
                ->filter(function ($c) use ($inicio, $fin) {
                    $cInicio = $this->citaInicio($c);
                    $cFin    = $this->citaFin($c);
                    return $cInicio->lt($fin) && $cFin->gt($inicio);
                });
            if ($choqueVehiculo->isNotEmpty()) {
                $validator->errors()->add('vehiculo_id', 'Este vehículo ya tiene una cita que se cruza con el horario seleccionado.');
            }

            // Choque del mismo mecánico
            if ($mecanicoId) {
                $choqueMecanico = Cita::query()
                    ->where('mecanico_id', $mecanicoId)
                    ->where('estado', '!=', 'cancelada')
                    ->whereRaw('DATE(fecha) = ?', [$fecha])
                    ->when($citaId, fn ($q) => $q->where('id', '!=', $citaId))
                    ->get()
                    ->filter(function ($c) use ($inicio, $fin) {
                        $cInicio = $this->citaInicio($c);
                        $cFin    = $this->citaFin($c);
                        return $cInicio->lt($fin) && $cFin->gt($inicio);
                    });
                if ($choqueMecanico->isNotEmpty()) {
                    $validator->errors()->add('mecanico_id', 'Este mecánico ya tiene una cita que se cruza con el horario seleccionado.');
                }
            }
        });
    }

    protected function calcularHoraFinPorDefecto(string $hora): string
    {
        try {
            return Carbon::createFromFormat('H:i', $hora)->addHour()->format('H:i');
        } catch (\Throwable $e) {
            return $hora;
        }
    }

    protected function citaInicio(Cita $c): Carbon
    {
        return Carbon::createFromFormat('Y-m-d H:i', $c->fecha->format('Y-m-d') . ' ' . $c->hora);
    }

    protected function citaFin(Cita $c): Carbon
    {
        $h = $c->hora_fin ?: Carbon::createFromFormat('H:i', $c->hora)->addHour()->format('H:i');
        return Carbon::createFromFormat('Y-m-d H:i', $c->fecha->format('Y-m-d') . ' ' . $h);
    }

    public function attributes(): array
    {
        return [
            'cliente_id' => 'cliente',
            'vehiculo_id' => 'vehículo',
            'sucursal_id' => 'sucursal',
            'servicio_id' => 'servicio',
            'mecanico_id' => 'mecánico',
            'fecha' => 'fecha',
            'hora' => 'hora',
            'tipo' => 'tipo',
            'descripcion_problema' => 'descripción del problema',
            'costo_consulta' => 'costo de consulta',
            'deja_vehiculo' => 'deja vehículo',
            'observaciones' => 'observaciones',
            'estado' => 'estado',
            'motivo_reprogramacion' => 'motivo de reprogramación',
            'cancelado_motivo' => 'motivo de cancelación',
        ];
    }
}
