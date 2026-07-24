@extends('layouts.admin')

@section('title', 'Nueva cita')
@section('navbar-title', 'Nueva cita')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Inicio</a></li>
    <li><a href="{{ route('admin.citas.index') }}">Citas</a></li>
    <li class="active" aria-current="page">Nueva</li>
@endsection

@section('content')
    <x-admin.page-header
        title="Nueva cita"
        description="Usa el calendario para crear la cita.">
        <x-slot:actions>
            <a href="{{ route('admin.citas.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Volver al calendario
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="admin-table-wrap p-4 text-center">
        <p class="text-muted">Esta acción se realiza desde el calendario.</p>
        <a href="{{ route('admin.citas.index') }}" class="btn btn-primary">
            <i class="bi bi-calendar-check" aria-hidden="true"></i>
            Ir al calendario
        </a>
    </div>
@endsection
