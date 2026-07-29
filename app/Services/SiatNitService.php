<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class SiatNitService
{
    private string $wsdl;

    public function __construct()
    {
        $this->wsdl = config('app.env') === 'production'
            ? 'https://siatrest.impuestos.gob.bo/ServicioFacturacion/ConsultaNit?wsdl'
            : 'https://pilotosiatservicios.impuestos.gob.bo/ServicioFacturacion/ConsultaNit?wsdl';
    }

    public function verificar(string $nit): array
    {
        $nitUsuario = Setting::obtener('nit', '');
        $codigoSistema = Setting::obtener('codigo_sistema', '');

        if (! $nitUsuario || ! $codigoSistema) {
            return [
                'valido' => false,
                'error' => 'Falta configurar NIT del taller o código de sistema en Configuración.',
            ];
        }

        try {
            $client = new \SoapClient($this->wsdl, [
                'connection_timeout' => 10,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'trace' => true,
                'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
            ]);

            $response = $client->verificarNit([
                'nit' => (int) $nit,
                'codigoSistema' => $codigoSistema,
                'nitUsuario' => (int) $nitUsuario,
            ]);

            $resultado = $response?->RespuestaConsultaNit ?? null;

            if (! $resultado) {
                return ['valido' => false, 'error' => 'Respuesta vacía del SIN.'];
            }

            $codigo = (int) ($resultado->codigoRespuesta ?? -1);

            if ($codigo === 1) {
                return [
                    'valido' => true,
                    'nit' => $nit,
                    'razon_social' => $resultado->razonSocial ?? '',
                    'estado' => $resultado->estado ?? '',
                ];
            }

            $mensajes = [
                2 => 'NIT inválido.',
                3 => 'NIT no registrado en SIN.',
                4 => 'NIT sin actividad económica.',
            ];

            return [
                'valido' => false,
                'error' => $mensajes[$codigo] ?? "Respuesta SIN: código {$codigo}.",
            ];
        } catch (\SoapFault $e) {
            Log::warning('SIAT NIT verification SOAP error: ' . $e->getMessage());
            return ['valido' => false, 'error' => 'No se pudo conectar con el SIN.'];
        } catch (\Throwable $e) {
            Log::warning('SIAT NIT verification error: ' . $e->getMessage());
            return ['valido' => false, 'error' => 'Error de conexión con el SIN.'];
        }
    }
}
