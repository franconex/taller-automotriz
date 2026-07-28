<?php

namespace App\Http\Requests\Cliente;

use App\Models\Cita;
use App\Models\Sucursal;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class CitaStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehiculo_id' => [
                'required',
                'exists:vehiculos,id',
                function ($attr, $value, $fail) {
                    $v = Vehiculo::find($value);
                    if (! $v) { $fail('El vehículo no existe.'); return; }
                    if ((int) $v->cliente_id !== (int) $this->user()->cliente_id) {
                        $fail('El vehículo no te pertenece.');
                    }
                },
            ],
            'servicio_id' => ['nullable', 'exists:servicios,id'],
            'sucursal_id' => [
                'required', 'exists:sucursales,id',
                function ($attr, $value, $fail) {
                    $s = Sucursal::find($value);
                    if ($s && ! $s->estado) $fail('La sucursal seleccionada está inactiva.');
                },
            ],
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'hora' => ['required', 'date_format:H:i'],
            'tipo' => ['required', 'in:diagnostico,mantenimiento,reparacion,otro'],
            'descripcion_problema' => ['nullable', 'string', 'max:2000'],
            'deja_vehiculo' => ['nullable', 'boolean'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) return;

            $fecha = $this->input('fecha');
            $hora = $this->input('hora');
            if (! $fecha || ! $hora) return;

            try {
                $inicio = Carbon::parse($fecha . ' ' . $hora);
                $fin = $inicio->copy()->addHour();
            } catch (\Throwable $e) {
                return;
            }

            if ($inicio->lessThan(now())) {
                $validator->errors()->add('hora', 'No puedes agendar en una hora pasada.');
                return;
            }

            $vehiculoId = $this->input('vehiculo_id');

            $choque = Cita::where('vehiculo_id', $vehiculoId)
                ->whereNotIn('estado', ['cancelada', 'rechazada', 'no_asistio'])
                ->whereDate('fecha', $fecha)
                ->get()
                ->filter(function ($c) use ($inicio, $fin) {
                    $cInicio = Carbon::parse($c->fecha->format('Y-m-d') . ' ' . $c->hora);
                    $cFin = Carbon::parse($c->fecha->format('Y-m-d') . ' ' . ($c->hora_fin ?: Carbon::parse($c->hora)->addHour()->format('H:i')));
                    return $cInicio->lt($fin) && $cFin->gt($inicio);
                });

            if ($choque->isNotEmpty()) {
                $validator->errors()->add('vehiculo_id', 'Este vehículo ya tiene una cita en ese horario.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'vehiculo_id' => 'vehículo',
            'servicio_id' => 'servicio',
            'sucursal_id' => 'sucursal',
            'fecha' => 'fecha',
            'hora' => 'hora',
            'tipo' => 'tipo',
            'descripcion_problema' => 'descripción del problema',
            'deja_vehiculo' => 'deja vehículo',
            'observaciones' => 'observaciones',
        ];
    }
}
