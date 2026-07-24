@extends('layouts.admin')

@section('title', 'Editar cita')
@section('navbar-title', 'Editar cita')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.citas.index') }}">Citas</a></li>
    <li class="active" aria-current="page">Editar</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Editar cita"
        description="Usa el modal de detalle del calendario.">
        <x-slot:actions>
            <a href="{{ route('admin.citas.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver al calendario
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4 text-center">
        <p class="text-muted">La edición se realiza desde el calendario. Abre el detalle de la cita para editarla.</p>
        <a href="{{ route('admin.citas.index') }}" class="btn btn-primary">
            <i class="bi bi-calendar-check" aria-hidden="true"></i>
            Ir al calendario
        </a>
    </div>
@endsection
