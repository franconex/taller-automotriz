@extends('layouts.admin')
@section('title', 'Crear Método de Pago')
@section('page-title', 'Crear Método de Pago')
@section('content')
    @include('admin._catalogo-form', [
        'action' => route('admin.metodos-pago.store'),
        'method' => 'POST',
        'fields' => ['nombre', 'descripcion'],
        'labels' => ['Nombre del método', 'Descripción'],
        'placeholders' => ['Ej. Efectivo', 'Pago en efectivo en el taller'],
        'badge_field' => 'activo',
        'badge_label' => 'Método activo',
    ])
@endsection
