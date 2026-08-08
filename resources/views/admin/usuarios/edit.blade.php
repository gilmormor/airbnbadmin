@extends('layouts.app')

@section('page-title', 'Editar usuario')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('usuarios.update', $usuario) }}">
                @csrf
                @method('PUT')
                @include('admin.usuarios._form')
            </form>
        </div>
    </div>
@endsection
