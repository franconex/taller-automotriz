@extends('layouts.admin')
@section('title', 'Editar Tipo de Servicio')
@section('page-title', 'Editar Tipo de Servicio')
@section('content')
    @include('admin._catalogo-form', [
        'action' => route('admin.tipo-servicios.update', $tipoServicio),
        'method' => 'PUT',
        'item' => $tipoServicio,
        'fields' => ['nombre', 'descripcion'],
        'labels' => ['Nombre del tipo', 'Descripción'],
    ])
@endsection
