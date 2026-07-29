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
        $nit = preg_replace('/\D/', '', $nit);

        if (! $this->validarFormato($nit)) {
            return ['valido' => false, 'error' => 'NIT debe tener entre 7 y 15 dígitos numéricos.'];
        }

        $nitUsuario = Setting::obtener('nit', '');
        $codigoSistema = Setting::obtener('codigo_sistema', '');

        if ($nitUsuario && $codigoSistema) {
            $resultado = $this->consultarSin($nit, $nitUsuario, $codigoSistema);
            if ($resultado !== null) {
                return $resultado;
            }
        }

        if ($this->validarMod11($nit)) {
            return [
                'valido' => true,
                'nit' => $nit,
                'razon_social' => null,
                'estado' => 'validado por formato',
            ];
        }

        return ['valido' => false, 'error' => 'NIT inválido - dígito verificador incorrecto.'];
    }

    private function validarFormato(string $nit): bool
    {
        return preg_match('/^\d{7,15}$/', $nit) === 1;
    }

    private function validarMod11(string $nit): bool
    {
        if (strlen($nit) < 2) return false;

        $digitoVerificador = (int) substr($nit, -1);
        $base = substr($nit, 0, -1);

        $factor = 2;
        $suma = 0;

        for ($i = strlen($base) - 1; $i >= 0; $i--) {
            $suma += (int) $base[$i] * $factor;
            $factor = $factor < 7 ? $factor + 1 : 2;
        }

        $resto = $suma % 11;
        $digitoCalculado = 11 - $resto;

        if ($digitoCalculado === 11) $digitoCalculado = 0;
        if ($digitoCalculado === 10) $digitoCalculado = 1;

        return $digitoCalculado === $digitoVerificador;
    }

    private function consultarSin(string $nit, string $nitUsuario, string $codigoSistema): ?array
    {
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
                return null;
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
        } catch (\Throwable $e) {
            Log::warning('SIAT NIT verification SOAP error: ' . $e->getMessage());
            return null;
        }
    }
}
