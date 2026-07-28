@extends('layouts.admin')

@section('title', 'Nuevo subservicio')
@section('navbar-title', 'Nuevo subservicio')

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.subservicios.index') }}" class="text-decoration-none small">&larr; Volver</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3"><h5 class="mb-0">Nuevo subservicio</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.subservicios.store') }}">
                @csrf
                @include('admin.subservicios._form', ['subservicio' => null])
                <button type="submit" class="btn text-white mt-3" style="background:#E31E24;">Crear subservicio</button>
            </form>
        </div>
    </div>
@endsection
