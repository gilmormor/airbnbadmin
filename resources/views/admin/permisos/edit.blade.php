@extends('layouts.app')

@section('page-title', 'Editar permiso')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('permisos.update', $permiso) }}">
                @csrf
                @method('PUT')
                @include('admin.permisos._form')
            </form>
        </div>
    </div>
@endsection
