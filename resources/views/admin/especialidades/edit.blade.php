@extends('layouts.admin')
@section('title', 'Editar Especialidad')
@section('page-title', 'Editar Especialidad')
@section('content')
    @include('admin._catalogo-form', [
        'action' => route('admin.especialidades.update', $especialidade),
        'method' => 'PUT',
        'item' => $especialidade,
    ])
@endsection
