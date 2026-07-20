@extends('layouts.admin')
@section('title', 'Crear Especialidad')
@section('page-title', 'Crear Especialidad')
@section('content')
    @include('admin._catalogo-form', [
        'action' => route('admin.especialidades.store'),
        'method' => 'POST',
    ])
@endsection
