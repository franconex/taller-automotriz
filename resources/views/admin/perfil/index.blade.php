@extends('layouts.admin')

@section('title', 'Mi perfil')
@section('navbar-title', 'Mi perfil')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Mi perfil</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Mi perfil"
        description="Información personal y credenciales de acceso." />

    @php $empleado = $usuario->empleado; @endphp

    <form method="POST" action="{{ route('admin.perfil.update') }}" id="form-perfil">
        @csrf
        @method('PUT')

        <div class="row g-3">
            {{-- Foto --}}
            <div class="col-12 col-lg-4">
                <div class="admin-table-wrap p-4 text-center">
                    <h2 class="h6 fw-bold mb-3">Foto de perfil</h2>
                    <div class="mb-3" id="foto-preview">
                        @if ($usuario->perfil && $usuario->perfil->foto_url)
                            <img src="{{ $usuario->perfil->foto_url }}"
                                 alt="Foto de {{ $empleado->nombre_completo ?? $usuario->nombre }}"
                                 class="rounded-circle"
                                 style="width:150px;height:150px;object-fit:cover;">
                        @else
                            <span class="admin-avatar d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary text-white fw-bold"
                                  style="width:150px;height:150px;font-size:3rem;">
                                {{ $usuario->nombre ? mb_strtoupper(mb_substr($usuario->nombre, 0, 1)) : 'U' }}
                            </span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="field-foto" class="form-label d-block">Cambiar foto</label>
                        <input type="file"
                               name="foto"
                               id="field-foto"
                               class="form-control"
                               accept="image/jpeg,image/png,image/webp">
                        <div class="form-text mt-1">JPG, PNG o WebP. Máximo 10 MB.</div>
                        <div class="invalid-feedback d-none" id="foto-error"></div>
                        <div class="small text-success d-none" id="foto-ok"></div>
                    </div>
                    @if ($usuario->perfil && $usuario->perfil->foto)
                        <div class="mt-2">
                            <label class="small text-muted">
                                <input type="checkbox" name="eliminar_foto" value="1" id="eliminar-foto-check">
                                Eliminar foto actual
                            </label>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Datos --}}
            <div class="col-12 col-lg-8">
                <div class="admin-table-wrap p-4 mb-3">
                    <h2 class="h6 fw-bold mb-3">
                        <i class="bi bi-person-badge me-1" aria-hidden="true"></i>
                        Datos personales
                        @if ($empleado)
                            <span class="cell-muted small fw-normal ms-2">(heredados del empleado)</span>
                        @endif
                    </h2>
                    <dl class="admin-meta">
                        <dt>Nombre completo</dt>
                        <dd>{{ $empleado->nombre_completo ?? $usuario->nombre }}</dd>
                        @if ($empleado)
                            <dt>CI</dt><dd>{{ $empleado->ci ?? '—' }}</dd>
                            <dt>Teléfono</dt><dd>{{ $empleado->telefono ?? '—' }}</dd>
                            <dt>Correo</dt><dd>{{ $empleado->email ?? $usuario->email }}</dd>
                            <dt>Dirección</dt><dd>{{ $empleado->direccion ?? '—' }}</dd>
                            <dt>Rol</dt><dd>{{ $empleado->rol->nombre ?? $usuario->rol->nombre ?? '—' }}</dd>
                            @if ($empleado->cargo)
                                <dt>Cargo</dt><dd>{{ $empleado->cargo }}</dd>
                            @endif
                            <dt>Sucursal</dt><dd>{{ $empleado->sucursal->nombre ?? '—' }}</dd>
                        @else
                            <dt>Correo</dt><dd>{{ $usuario->email ?? '—' }}</dd>
                            <dt>Rol</dt><dd>{{ $usuario->rol->nombre ?? '—' }}</dd>
                        @endif
                    </dl>
                    @if ($empleado)
                    <p class="cell-muted small mb-0">
                        <i class="bi bi-info-circle" aria-hidden="true"></i>
                        Para modificar estos datos, ve a <a href="{{ route('admin.empleados.index') }}">Empleados</a>.
                    </p>
                    @endif
                </div>

                <div class="admin-table-wrap p-4">
                    <h2 class="h6 fw-bold mb-3">
                        <i class="bi bi-key me-1" aria-hidden="true"></i>
                        Credenciales de acceso
                    </h2>
                    <dl class="admin-meta mb-3">
                        <dt>Correo electrónico</dt>
                        <dd class="text-break">{{ $usuario->email ?? ($empleado->email ?? '—') }}</dd>
                    </dl>
                    <x-admin.form-field
                        name="username"
                        label="Nombre de usuario"
                        :value="$usuario->username"
                        required
                        icon="bi-at" />

                    <hr class="my-3">
                    <h3 class="h6 fw-bold mb-2">Cambiar contraseña</h3>
                    <p class="cell-muted small mb-3">Deja los campos en blanco si no deseas cambiarla.</p>
                    <x-admin.form-field
                        name="password"
                        type="password"
                        label="Nueva contraseña"
                        icon="bi-lock"
                        autocomplete="new-password" />
                    <x-admin.form-field
                        name="password_confirmation"
                        type="password"
                        label="Confirmar contraseña"
                        icon="bi-lock-fill"
                        autocomplete="new-password" />

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check2" aria-hidden="true"></i>
                            Guardar cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
document.getElementById('field-foto')?.addEventListener('change', function (e) {
    const file = e.target.files[0];
    const errorEl = document.getElementById('foto-error');
    const okEl = document.getElementById('foto-ok');
    const preview = document.getElementById('foto-preview');
    if (!file) return;
    okEl.classList.add('d-none');
    errorEl.classList.add('d-none');
    if (!['image/jpeg','image/png','image/webp'].includes(file.type)) {
        errorEl.textContent = 'Solo se permiten JPG, PNG o WebP.';
        errorEl.classList.remove('d-none');
        this.value = '';
        return;
    }
    if (file.size > 10 * 1024 * 1024) {
        errorEl.textContent = 'La imagen no debe superar los 10 MB.';
        errorEl.classList.remove('d-none');
        this.value = '';
        return;
    }
    const reader = new FileReader();
    reader.onload = function (ev) {
        const img = new Image();
        img.onload = async function () {
            const MAX = 200;
            let w = img.width, h = img.height;
            if (w > MAX || h > MAX) {
                const ratio = Math.min(MAX / w, MAX / h);
                w = Math.round(w * ratio);
                h = Math.round(h * ratio);
            }
            const canvas = document.createElement('canvas');
            canvas.width = w;
            canvas.height = h;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, w, h);
            const dataUri = canvas.toDataURL('image/jpeg', 0.6);
            preview.innerHTML = `<img src="${dataUri}" alt="Vista previa" class="rounded-circle" style="width:150px;height:150px;object-fit:cover;">`;
            okEl.textContent = 'Guardando foto...';
            okEl.classList.remove('d-none');
            try {
                const res = await fetch('{{ route('admin.perfil.foto') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ foto: dataUri }),
                });
                const r = await res.json();
                if (r.ok) {
                    okEl.textContent = 'Foto guardada. Actualizando...';
                    window.location.reload();
                } else {
                    errorEl.textContent = r.message || 'Error al guardar la foto.';
                    errorEl.classList.remove('d-none');
                    okEl.classList.add('d-none');
                }
            } catch (err) {
                errorEl.textContent = 'Error de conexión al guardar la foto.';
                errorEl.classList.remove('d-none');
                okEl.classList.add('d-none');
            }
        };
        img.src = ev.target.result;
    };
    reader.readAsDataURL(file);
});
</script>
@endpush
