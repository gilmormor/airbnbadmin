@extends('layouts.app')

@section('page-title', 'Nuevo usuario')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('usuarios.store') }}">
                @csrf
                @include('admin.usuarios._form')
            </form>
        </div>
    </div>
@endsection
