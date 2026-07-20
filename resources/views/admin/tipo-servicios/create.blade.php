@extends('layouts.admin')
@section('title', 'Crear Tipo de Servicio')
@section('page-title', 'Crear Tipo de Servicio')
@section('content')
    @include('admin._catalogo-form', [
        'action' => route('admin.tipo-servicios.store'),
        'method' => 'POST',
        'fields' => ['nombre', 'descripcion'],
        'labels' => ['Nombre del tipo', 'Descripción'],
        'placeholders' => ['Ej. Mantenimiento', 'Servicios de mantenimiento preventivo'],
    ])
@endsection
