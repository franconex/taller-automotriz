<?php

namespace App\Services;

use App\Models\Auditoria;
use App\Models\Cliente;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ClienteAccountService
{
    private Rol $rolCliente;

    public function __construct()
    {
        $this->rolCliente = Rol::firstOrCreate(
            ['nombre' => 'Cliente'],
            ['descripcion' => 'Portal de seguimiento para clientes', 'estado' => true]
        );
    }

    public function registrarManual(array $data): User
    {
        $email = strtolower(trim($data['email']));

        $this->verificarCorreoNoInterno($email);

        return DB::transaction(function () use ($email, $data) {
            $cliente = Cliente::where('email', $email)->first();

            if ($cliente) {
                $this->verificarClienteSinUser($cliente, $email);
            } else {
                $cliente = Cliente::create([
                    'nombre_completo' => $data['nombre_completo'],
                    'email' => $email,
                    'telefono' => $data['telefono'] ?? null,
                    'ci' => $data['ci'] ?? null,
                    'fecha_registro' => now(),
                    'estado' => true,
                ]);
            }

            $user = User::create([
                'cliente_id' => $cliente->id,
                'rol_id' => $this->rolCliente->id,
                'nombre' => $data['nombre_completo'],
                'username' => explode('@', $email)[0] . '_' . uniqid(),
                'email' => $email,
                'password' => Hash::make($data['password']),
                'estado' => 'activo',
                'origen_registro' => 'manual',
            ]);

            $this->auditar('crear', $user, ['origen' => 'registro_manual']);

            return $user;
        });
    }

    public function registrarConGoogle(object $googleUser): User
    {
        $email = strtolower(trim($googleUser->email ?? ''));
        if (! $email) {
            throw ValidationException::withMessages(['email' => 'Google no proporcionó un correo electrónico.']);
        }

        $this->verificarCorreoNoInterno($email);

        return DB::transaction(function () use ($googleUser, $email) {
            $existingByGoogle = User::where('google_id', $googleUser->id)->first();
            if ($existingByGoogle) {
                if ($existingByGoogle->google_avatar !== $googleUser->avatar) {
                    $existingByGoogle->update(['google_avatar' => $googleUser->avatar]);
                }
                return $existingByGoogle;
            }

            $existingByEmail = User::where('email', $email)->first();
            if ($existingByEmail && $existingByEmail->esCliente()) {
                $existingByEmail->update([
                    'google_id' => $googleUser->id,
                    'google_avatar' => $googleUser->avatar,
                    'email_verified_at' => $existingByEmail->email_verified_at ?? now(),
                ]);
                $this->auditar('vincular_google', $existingByEmail, []);
                return $existingByEmail;
            }

            $cliente = Cliente::where('email', $email)->first();
            if ($cliente) {
                $this->verificarClienteSinUser($cliente, $email);
            } else {
                $cliente = Cliente::create([
                    'nombre_completo' => $googleUser->name ?? $googleUser->nickname ?? 'Sin nombre',
                    'email' => $email,
                    'telefono' => $googleUser->user['phone_number'] ?? null,
                    'fecha_registro' => now(),
                    'estado' => true,
                ]);
            }

            $user = User::create([
                'cliente_id' => $cliente->id,
                'rol_id' => $this->rolCliente->id,
                'nombre' => $cliente->nombre_completo,
                'username' => explode('@', $email)[0] . '_' . uniqid(),
                'email' => $email,
                'google_id' => $googleUser->id,
                'google_avatar' => $googleUser->avatar,
                'email_verified_at' => $googleUser->user['email_verified'] ?? false ? now() : null,
                'estado' => 'activo',
                'origen_registro' => 'google',
            ]);

            $this->auditar('crear_google', $user, []);

            return $user;
        });
    }

    public function crearDesdeInvitacion(string $token, string $password): User
    {
        $invitacion = \App\Models\InvitacionCliente::where('token_hash', hash('sha256', $token))
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (! $invitacion) {
            throw ValidationException::withMessages(['token' => 'El enlace de invitación no es válido o ha expirado.']);
        }

        $cliente = $invitacion->cliente;

        return DB::transaction(function () use ($cliente, $invitacion, $password) {
            $user = User::create([
                'cliente_id' => $cliente->id,
                'rol_id' => $this->rolCliente->id,
                'nombre' => $cliente->nombre_completo,
                'username' => explode('@', $cliente->email)[0] . '_' . uniqid(),
                'email' => $cliente->email,
                'password' => Hash::make($password),
                'estado' => 'activo',
                'origen_registro' => 'invitacion',
                'debe_cambiar_password' => true,
            ]);

            $invitacion->update(['used_at' => now()]);

            $this->auditar('crear_invitacion', $user, ['invitacion_id' => $invitacion->id]);

            return $user;
        });
    }

    public function verificarCorreoNoInterno(string $email): void
    {
        $existing = User::where('email', $email)->first();

        if (! $existing) return;

        // Si ya es cliente, no es un error — se vinculará más adelante
        if ($existing->esCliente()) return;

        // Si tiene un empleado asociado, es un usuario interno
        if ($existing->empleado) {
            throw ValidationException::withMessages([
                'email' => 'Este correo pertenece a un usuario del sistema. Usa el inicio de sesión del personal.',
            ]);
        }

        // Si es otro tipo de usuario interno (admin, gerente, etc.)
        throw ValidationException::withMessages([
            'email' => 'Este correo ya está registrado en el sistema. Inicia sesión con tu contraseña.',
        ]);
    }

    public function verificarClienteSinUser(Cliente $cliente, string $email): void
    {
        if (User::where('cliente_id', $cliente->id)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Este correo ya está registrado. Inicia sesión o recupera tu contraseña.',
            ]);
        }
    }

    private function auditar(string $accion, User $user, array $extra = []): void
    {
        Auditoria::create(array_merge([
            'usuario_id' => $user->id,
            'accion' => $accion,
            'modulo' => 'auth',
            'entidad_tipo' => 'User',
            'entidad_id' => $user->id,
            'datos_nuevos' => json_encode([
                'email' => $user->email,
                'rol' => 'Cliente',
                'origen' => $user->origen_registro,
            ] + $extra),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'fecha_accion' => now(),
        ], $extra));
    }
}
