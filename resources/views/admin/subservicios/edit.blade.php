@extends('layouts.admin')

@section('title', 'Editar subservicio')
@section('navbar-title', 'Editar subservicio')

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.subservicios.index') }}" class="text-decoration-none small">&larr; Volver</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3"><h5 class="mb-0">Editar: {{ $subservicio->nombre }}</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.subservicios.update', $subservicio) }}">
                @csrf @method('PUT')
                @include('admin.subservicios._form', ['subservicio' => $subservicio])
                <button type="submit" class="btn text-white mt-3" style="background:#E31E24;">Actualizar</button>
            </form>
        </div>
    </div>
@endsection
