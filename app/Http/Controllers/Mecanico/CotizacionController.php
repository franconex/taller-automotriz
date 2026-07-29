<?php

namespace App\Http\Controllers\Mecanico;

use App\Http\Controllers\Controller;
use App\Models\AsignacionTrabajo;
use App\Models\Autorizacion;
use App\Models\Cita;
use App\Models\Mecanico;
use App\Models\OrdenTrabajo;
use App\Models\OrdenServicio;
use App\Models\OrdenRepuesto;
use App\Models\Servicio;
use App\Models\Repuesto;
use App\Models\User;
use App\Notifications\CotizacionEnviada;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CotizacionController extends Controller
{
    private function mecanicoId(): ?int
    {
        return Auth::user()?->empleado?->mecanico?->id;
    }

    private function mecanico(): ?Mecanico
    {
        $id = $this->mecanicoId();
        return $id ? Mecanico::with('empleado')->find($id) : null;
    }

    /**
     * Iniciar trabajo desde una cita (cliente presente).
     * Crea orden + asignación, cita → atendida.
     */
    public function iniciarTrabajo(Request $request, Cita $cita): RedirectResponse
    {
        if ($cita->estado !== 'confirmada') {
            return redirect()->route('mecanico.dashboard')
                ->with('error', 'Solo se puede iniciar trabajo en citas confirmadas.');
        }

        $mecanico = $this->mecanico();
        if (! $mecanico) {
            return redirect()->route('mecanico.dashboard')
                ->with('error', 'No se encontró tu perfil de mecánico.');
        }

        try {
            $orden = DB::transaction(function () use ($cita, $mecanico) {
                // Crear orden
                $siguienteId = (OrdenTrabajo::withTrashed()->max('id') ?? 0) + 1;
                $numeroOrden = 'OT-' . str_pad((string) $siguienteId, 6, '0', STR_PAD_LEFT);

                $orden = OrdenTrabajo::create([
                    'numero_orden'          => $numeroOrden,
                    'cliente_id'            => $cita->cliente_id,
                    'vehiculo_id'           => $cita->vehiculo_id,
                    'sucursal_id'           => $cita->sucursal_id,
                    'usuario_recepcion_id'  => Auth::id(),
                    'cita_id'               => $cita->id,
                    'fecha_emision'         => now(),
                    'fecha_inicio'          => now(),
                    'descripcion_problema'  => $cita->descripcion_problema,
                    'estado'                => 'recibida',
                    'origen_ingreso'        => 'cita',
                    'descuento'             => 0,
                    'kilometraje_ingreso'   => 0,
                ]);

                // Crear asignación
                AsignacionTrabajo::create([
                    'orden_trabajo_id'     => $orden->id,
                    'mecanico_id'          => $mecanico->id,
                    'usuario_asignador_id' => Auth::id(),
                    'actividad_asignada'   => $cita->descripcion_problema ?? 'Atención programada',
                    'prioridad'            => 'normal',
                    'estado'               => 'pendiente',
                    'fecha_asignacion'     => now(),
                    'fecha_inicio'         => now(),
                ]);

                // Cita → atendida
                $cita->update([
                    'estado_anterior' => $cita->estado,
                    'estado' => 'atendida',
                ]);

                return $orden;
            });
        } catch (\Throwable $e) {
            return redirect()->route('mecanico.dashboard')
                ->with('error', 'Error al iniciar trabajo: ' . $e->getMessage());
        }

        return redirect()->route('mecanico.ordenes.show', $orden)
            ->with('success', 'Trabajo iniciado. Orden ' . $orden->numero_orden . ' creada.');
    }

    /**
     * Mostrar formulario de cotización desde una cita.
     */
    public function create(Cita $cita): View|RedirectResponse
    {
        if ($cita->estado !== 'confirmada') {
            return redirect()->route('mecanico.dashboard')
                ->with('error', 'Solo se puede cotizar en citas confirmadas.');
        }

        $mecanico = $this->mecanico();
        if (! $mecanico) {
            return redirect()->route('mecanico.dashboard')
                ->with('error', 'No se encontró tu perfil de mecánico.');
        }

        // Buscar cotización pendiente existente para esta cita (con o sin items)
        $autorizacion = Autorizacion::where('cita_id', $cita->id)
            ->where('estado', 'pendiente')
            ->first();

        // Si no existe ninguna, crear una nueva
        if (! $autorizacion) {
            $autorizacion = Autorizacion::create([
                'cita_id'               => $cita->id,
                'usuario_solicitante_id' => Auth::id(),
                'titulo'                => 'Cotización - ' . ($cita->cliente?->nombre_completo ?? 'Cliente'),
                'descripcion'           => $cita->descripcion_problema ?? 'Presupuesto de reparación',
                'importe'               => 0,
                'estado'                => 'pendiente',
                'fecha_solicitud'       => now(),
            ]);
        }

        // Servicios y repuestos de la cotización
        $servicios = OrdenServicio::where('autorizacion_id', $autorizacion->id)->get();
        $repuestos = OrdenRepuesto::where('autorizacion_id', $autorizacion->id)->get();

        $totalServicios = $servicios->sum('precio_base');
        $totalRepuestos = $repuestos->sum(DB::raw('cantidad * precio_unitario_snapshot'));

        // Auto-calcular tiempo estimado desde servicios
        $minutosDesdeServicios = $servicios->sum('tiempo_estimado_minutos');

        // Si el mecánico ya guardó tiempo, respetarlo; si no, auto-calcular
        if ($autorizacion->tiempo_estimado_minutos && $autorizacion->tiempo_estimado_unidad) {
            $autoCalculado = false;
            $tiempoUnidad = $autorizacion->tiempo_estimado_unidad;
            $tiempoValor = match ($tiempoUnidad) {
                'dias' => round($autorizacion->tiempo_estimado_minutos / 1440),
                'horas' => round($autorizacion->tiempo_estimado_minutos / 60),
                default => $autorizacion->tiempo_estimado_minutos,
            };
        } else {
            $autoCalculado = true;
            // Elegir mejor unidad
            if ($minutosDesdeServicios >= 1440) {
                $tiempoUnidad = 'dias';
                $tiempoValor = round($minutosDesdeServicios / 1440, 1);
            } elseif ($minutosDesdeServicios >= 60) {
                $tiempoUnidad = 'horas';
                $tiempoValor = round($minutosDesdeServicios / 60, 1);
            } else {
                $tiempoUnidad = 'minutos';
                $tiempoValor = $minutosDesdeServicios ?: 1;
            }
        }

        return view('mecanico.cotizacion.create', compact(
            'cita', 'autorizacion', 'servicios', 'repuestos',
            'totalServicios', 'totalRepuestos',
            'tiempoValor', 'tiempoUnidad', 'minutosDesdeServicios', 'autoCalculado'
        ));
    }

    /**
     * Agregar servicio a la cotización.
     */
    public function addServicio(Request $request, Autorizacion $autorizacion): RedirectResponse
    {
        if ($autorizacion->cita_id && $autorizacion->estado !== 'pendiente') {
            return back()->with('error', 'La cotización ya fue enviada.');
        }

        $data = $request->validate([
            'servicio_id' => ['required', 'exists:servicios,id'],
        ]);

        $servicio = Servicio::findOrFail($data['servicio_id']);

        OrdenServicio::create([
            'autorizacion_id'         => $autorizacion->id,
            'mecanico_id'             => $this->mecanicoId(),
            'servicio_id'             => $servicio->id,
            'nombre_servicio'         => $servicio->nombre,
            'precio_base'             => $servicio->precio_base ?? 0,
            'tiempo_estimado_minutos' => $servicio->duracion_estimada_minutos,
        ]);

        return redirect()->route('mecanico.cotizacion.create', $autorizacion->cita)
            ->with('success', 'Servicio agregado a la cotización.');
    }

    /**
     * Agregar repuesto a la cotización.
     */
    public function addRepuesto(Request $request, Autorizacion $autorizacion): RedirectResponse
    {
        if ($autorizacion->cita_id && $autorizacion->estado !== 'pendiente') {
            return back()->with('error', 'La cotización ya fue enviada.');
        }

        $data = $request->validate([
            'repuesto_id' => ['required', 'exists:repuestos,id'],
            'cantidad'    => ['required', 'numeric', 'min:0.01'],
        ]);

        $repuesto = Repuesto::findOrFail($data['repuesto_id']);

        OrdenRepuesto::create([
            'autorizacion_id'         => $autorizacion->id,
            'repuesto_id'             => $repuesto->id,
            'mecanico_id'             => $this->mecanicoId(),
            'cantidad'                => $data['cantidad'],
            'estado'                  => 'pendiente',
            'precio_unitario_snapshot' => $repuesto->precio_venta ?? 0,
        ]);

        return redirect()->route('mecanico.cotizacion.create', $autorizacion->cita)
            ->with('success', 'Repuesto agregado a la cotización.');
    }

    /**
     * Enviar cotización al cliente.
     */
    public function enviar(Request $request, Autorizacion $autorizacion): RedirectResponse
    {
        if ($autorizacion->estado !== 'pendiente') {
            return redirect()->route('mecanico.dashboard')
                ->with('error', 'La cotización ya fue enviada.');
        }

        $servicios = OrdenServicio::where('autorizacion_id', $autorizacion->id)->get();
        $repuestos = OrdenRepuesto::where('autorizacion_id', $autorizacion->id)->get();

        if ($servicios->isEmpty() && $repuestos->isEmpty()) {
            return back()->with('error', 'Agregá al menos un servicio o repuesto antes de enviar.');
        }

        $totalServicios = $servicios->sum('precio_base');
        $totalRepuestos = $repuestos->sum(DB::raw('cantidad * precio_unitario_snapshot'));
        $manoDeObra = (float) $request->input('mano_de_obra', 0);
        $importe = $totalServicios + $totalRepuestos + $manoDeObra;

        // Calcular minutos desde valor + unidad
        $tiempoValor = (float) $request->input('tiempo_estimado_valor', 0);
        $tiempoUnidad = $request->input('tiempo_estimado_unidad', 'horas');
        $multiplicador = match ($tiempoUnidad) {
            'dias' => 1440,
            'horas' => 60,
            default => 1,
        };
        $tiempoMinutos = $tiempoValor > 0 ? min((int) round($tiempoValor * $multiplicador), 43800) : null;

        // Guardar foto si se subió
        $fotoPath = $autorizacion->foto_diagnostico;
        if ($request->hasFile('foto_diagnostico')) {
            $fotoPath = $request->file('foto_diagnostico')->store('cotizaciones', 'public');
        }

        $autorizacion->update([
            'importe' => $importe,
            'diagnostico_mecanico' => $request->input('diagnostico_mecanico'),
            'foto_diagnostico' => $fotoPath,
            'mano_de_obra' => $manoDeObra > 0 ? $manoDeObra : null,
            'tiempo_estimado_minutos' => $tiempoMinutos,
            'tiempo_estimado_unidad' => $tiempoValor > 0 ? $tiempoUnidad : null,
            'fecha_solicitud' => now(),
        ]);

        // Notificar al cliente
        $cita = $autorizacion->cita;
        if ($cita?->cliente?->user) {
            $cita->cliente->user->notify(new CotizacionEnviada($autorizacion, $cita));
        }

        return redirect()->route('mecanico.dashboard')
            ->with('success', 'Cotización enviada al cliente. Esperando su aprobación.');
    }

    /**
     * Mostrar formulario de cotización desde una orden existente.
     */
    public function ordenCreate(OrdenTrabajo $orden): View|RedirectResponse
    {
        $mecanico = $this->mecanico();
        if (! $mecanico) {
            return redirect()->route('mecanico.dashboard')->with('error', 'No se encontró tu perfil de mecánico.');
        }

        $servicios = OrdenServicio::where('orden_trabajo_id', $orden->id)->get();
        $repuestos = OrdenRepuesto::where('orden_trabajo_id', $orden->id)->get();

        $totalServicios = $servicios->sum('precio_base');
        $totalRepuestos = $repuestos->sum(DB::raw('cantidad * precio_unitario_snapshot'));
        $minutosDesdeServicios = $servicios->sum('tiempo_estimado_minutos');

        // Elegir mejor unidad para tiempo
        if ($minutosDesdeServicios >= 1440) {
            $tiempoUnidad = 'dias';
            $tiempoValor = round($minutosDesdeServicios / 1440, 1);
        } elseif ($minutosDesdeServicios >= 60) {
            $tiempoUnidad = 'horas';
            $tiempoValor = round($minutosDesdeServicios / 60, 1);
        } else {
            $tiempoUnidad = 'minutos';
            $tiempoValor = $minutosDesdeServicios ?: 1;
        }
        $autoCalculado = true;

        return view('mecanico.cotizacion.orden-create', compact(
            'orden', 'servicios', 'repuestos', 'totalServicios', 'totalRepuestos',
            'tiempoValor', 'tiempoUnidad', 'minutosDesdeServicios', 'autoCalculado'
        ));
    }

    /**
     * Enviar cotización desde una orden existente (servicios+repuestos+mano de obra).
     */
    public function ordenEnviar(Request $request, OrdenTrabajo $orden): RedirectResponse
    {
        $mecanico = $this->mecanico();
        if (! $mecanico) {
            return redirect()->route('mecanico.dashboard')->with('error', 'No se encontró tu perfil de mecánico.');
        }

        $servicios = OrdenServicio::where('orden_trabajo_id', $orden->id)->get();
        $repuestos = OrdenRepuesto::where('orden_trabajo_id', $orden->id)->get();

        if ($servicios->isEmpty() && $repuestos->isEmpty()) {
            return back()->with('error', 'Agregá al menos un servicio o repuesto antes de enviar.');
        }

        $totalServicios = $servicios->sum('precio_base');
        $totalRepuestos = $repuestos->sum(DB::raw('cantidad * precio_unitario_snapshot'));
        $manoDeObra = (float) $request->input('mano_de_obra', 0);
        $importe = $totalServicios + $totalRepuestos + $manoDeObra;

        $tiempoValor = (float) $request->input('tiempo_estimado_valor', 0);
        $tiempoUnidad = $request->input('tiempo_estimado_unidad', 'horas');
        $multiplicador = match ($tiempoUnidad) {
            'dias' => 1440,
            'horas' => 60,
            default => 1,
        };
        $tiempoMinutos = $tiempoValor > 0 ? min((int) round($tiempoValor * $multiplicador), 43800) : null;

        // Guardar foto si se subió
        $fotoPath = null;
        if ($request->hasFile('foto_diagnostico')) {
            $fotoPath = $request->file('foto_diagnostico')->store('cotizaciones', 'public');
        }

        DB::transaction(function () use ($orden, $request, $importe, $manoDeObra, $tiempoMinutos, $tiempoUnidad, $tiempoValor, $fotoPath) {
            \App\Models\Autorizacion::create([
                'orden_trabajo_id'            => $orden->id,
                'usuario_solicitante_id'      => Auth::id(),
                'titulo'                      => 'Cotización adicional - ' . $orden->numero_orden,
                'descripcion'                 => 'Nuevos trabajos detectados en la orden ' . $orden->numero_orden,
                'diagnostico_mecanico'        => $request->input('diagnostico_mecanico'),
                'foto_diagnostico'            => $fotoPath,
                'importe'                     => $importe,
                'mano_de_obra'                => $manoDeObra > 0 ? $manoDeObra : null,
                'tiempo_estimado_minutos'     => $tiempoMinutos,
                'tiempo_estimado_unidad'      => $tiempoValor > 0 ? $tiempoUnidad : null,
                'estado'                      => 'pendiente',
                'fecha_solicitud'             => now(),
            ]);

            $orden->update(['estado' => 'pendiente_autorizacion']);
        });

        // Notificar al cliente
        $autorizacion = $orden->autorizaciones()->latest()->first();
        if ($orden->cliente?->user && $autorizacion) {
            $orden->cliente->user->notify(new CotizacionEnviada($autorizacion, $orden->cita ?? new \App\Models\Cita()));
        }

        return redirect()->route('mecanico.ordenes.show', $orden)
            ->with('success', 'Cotización enviada al cliente. Esperando aprobación.');
    }
}
