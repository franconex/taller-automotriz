@extends('layouts.admin')

@section('title', 'Tipos de Servicio')

@section('page-title', 'Gestión de Tipos de Servicio')

@section('content')
    <x-admin.page-header
        title="Tipos de Servicio"
        subtitle="{{ $tipos->total() }} tipo(s)"
        :button="['label' => 'Nuevo tipo', 'url' => route('admin.tipo-servicios.create')]"
    />

    @if (session('success'))
        <x-admin.alert type="success">{{ session('success') }}</x-admin.alert>
    @endif

    @include('admin._catalogo-table', [
        'items' => $tipos,
        'headers' => ['Nombre', 'Descripción', 'Estado', 'Acciones'],
        'fields' => ['nombre', 'descripcion'],
        'ruta' => 'admin.tipo-servicios',
        'modelo' => 'tipoServicio',
    ])
@endsection
