@extends('layouts.admin')
@section('title', 'Editar Método de Pago')
@section('page-title', 'Editar Método de Pago')
@section('content')
    @include('admin._catalogo-form', [
        'action' => route('admin.metodos-pago.update', $metodoPago),
        'method' => 'PUT',
        'item' => $metodoPago,
        'fields' => ['nombre', 'descripcion'],
        'labels' => ['Nombre del método', 'Descripción'],
        'badge_field' => 'activo',
        'badge_label' => 'Método activo',
    ])
@endsection
