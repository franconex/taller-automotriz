@extends('layouts.admin')

@section('title', 'Métodos de Pago')

@section('page-title', 'Gestión de Métodos de Pago')

@section('content')
    <x-admin.page-header
        title="Métodos de Pago"
        subtitle="{{ $metodos->total() }} método(s)"
        :button="['label' => 'Nuevo método', 'url' => route('admin.metodos-pago.create')]"
    />

    @if (session('success'))
        <x-admin.alert type="success">{{ session('success') }}</x-admin.alert>
    @endif

    @include('admin._catalogo-table', [
        'items' => $metodos,
        'headers' => ['Nombre', 'Descripción', 'Estado', 'Acciones'],
        'fields' => ['nombre', 'descripcion'],
        'badge_field' => 'activo',
        'badge_activo_text' => 'Activo',
        'badge_inactivo_text' => 'Inactivo',
        'ruta' => 'admin.metodos-pago',
        'modelo' => 'metodoPago',
    ])
@endsection
