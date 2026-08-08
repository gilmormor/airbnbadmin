@extends('layouts.app')

@section('page-title', 'Editar departamento')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('departamentos.update', $departamento) }}">
                @csrf
                @method('PUT')
                @include('admin.departamentos._form')
            </form>
        </div>
    </div>
@endsection
