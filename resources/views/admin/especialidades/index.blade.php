@extends('layouts.admin')

@section('title', 'Especialidades')

@section('page-title', 'Gestión de Especialidades')

@section('content')
    @php $ruta = 'admin.especialidades'; $modelo = 'especialidade'; @endphp

    <x-admin.page-header
        title="Especialidades"
        subtitle="{{ $especialidades->total() }} especialidad(es)"
        :button="['label' => 'Nueva especialidad', 'url' => route('admin.especialidades.create')]"
    />

    @if (session('success'))
        <x-admin.alert type="success">{{ session('success') }}</x-admin.alert>
    @endif

    @include('admin._catalogo-table', [
        'items' => $especialidades,
        'headers' => ['Nombre', 'Descripción', 'Estado', 'Acciones'],
        'fields' => ['nombre', 'descripcion'],
    ])
@endsection
