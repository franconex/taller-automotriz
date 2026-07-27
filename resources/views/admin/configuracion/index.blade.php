@extends('layouts.admin')

@section('title', 'Configuración')
@section('navbar-title', 'Configuración')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li class="active" aria-current="page">Configuración</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Configuración"
        description="Ajustes generales del sistema y datos de la organización.">
    </x-admin.page-header>

    @if (Auth::user()->tienePermiso('configuracion.editar'))
    <form method="POST" action="{{ route('admin.configuracion.update') }}">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-12 col-lg-8">
                <div class="admin-table-wrap p-4">
                    <h2 class="h6 fw-bold mb-3">Datos de la organización</h2>
                    <p class="cell-muted mb-4" style="font-size:.9rem;">Información que aparece en comprobantes y reportes.</p>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <x-admin.form-field
                                name="razon_social"
                                label="Razón social"
                                :value="$config['razon_social']"
                                icon="bi-building" />
                        </div>
                        <div class="col-md-4">
                            <x-admin.form-field
                                name="nit"
                                label="NIT"
                                :value="$config['nit']"
                                icon="bi-upc" />
                        </div>
                        <div class="col-md-8">
                            <x-admin.form-field
                                name="direccion"
                                label="Dirección"
                                :value="$config['direccion']"
                                icon="bi-geo-alt" />
                        </div>
                        <div class="col-md-4">
                            <x-admin.form-field
                                name="telefono"
                                label="Teléfono"
                                :value="$config['telefono']"
                                icon="bi-telephone" />
                        </div>
                        <div class="col-12">
                            <x-admin.form-field
                                name="email"
                                type="email"
                                label="Correo de contacto"
                                :value="$config['email']"
                                icon="bi-envelope" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="admin-table-wrap p-4">
                    <h2 class="h6 fw-bold mb-3">Preferencias regionales</h2>

                    <x-admin.form-field
                        name="zona_horaria"
                        label="Zona horaria"
                        type="select">
                        <option value="America/La_Paz" @selected($config['zona_horaria'] === 'America/La_Paz')>America/La_Paz</option>
                        <option value="America/Lima" @selected($config['zona_horaria'] === 'America/Lima')>America/Lima</option>
                        <option value="America/Bogota" @selected($config['zona_horaria'] === 'America/Bogota')>America/Bogota</option>
                        <option value="America/Mexico_City" @selected($config['zona_horaria'] === 'America/Mexico_City')>America/Mexico_City</option>
                        <option value="America/Argentina/Buenos_Aires" @selected($config['zona_horaria'] === 'America/Argentina/Buenos_Aires')>America/Argentina/Buenos_Aires</option>
                    </x-admin.form-field>

                    <x-admin.form-field
                        name="moneda"
                        label="Moneda"
                        type="select">
                        <option value="BOB — Boliviano" @selected($config['moneda'] === 'BOB — Boliviano')>BOB — Boliviano</option>
                        <option value="USD — Dólar" @selected($config['moneda'] === 'USD — Dólar')>USD — Dólar</option>
                        <option value="PEN — Sol" @selected($config['moneda'] === 'PEN — Sol')>PEN — Sol</option>
                        <option value="ARS — Peso argentino" @selected($config['moneda'] === 'ARS — Peso argentino')>ARS — Peso argentino</option>
                        <option value="COP — Peso colombiano" @selected($config['moneda'] === 'COP — Peso colombiano')>COP — Peso colombiano</option>
                    </x-admin.form-field>

                    <x-admin.form-field
                        name="formato_fecha"
                        label="Formato de fecha"
                        type="select">
                        <option value="d/m/Y" @selected($config['formato_fecha'] === 'd/m/Y')>dd/mm/aaaa</option>
                        <option value="Y-m-d" @selected($config['formato_fecha'] === 'Y-m-d')>aaaa-mm-dd</option>
                        <option value="m/d/Y" @selected($config['formato_fecha'] === 'm/d/Y')>mm/dd/aaaa</option>
                    </x-admin.form-field>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
            <button type="reset" class="btn btn-outline-secondary">Descartar</button>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check2" aria-hidden="true"></i>
                Guardar cambios
            </button>
        </div>
    </form>
    @else
    <div class="admin-table-wrap p-4">
        <p class="cell-muted mb-0">Solo lectura — no tienes permisos para modificar la configuración.</p>
    </div>
    @endif
@endsection
