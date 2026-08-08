@extends('layouts.app')

@section('page-title', 'Editar propietario')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('propietarios.update', $propietario) }}">
                @csrf
                @method('PUT')
                @include('admin.propietarios._form')
            </form>
        </div>
    </div>
@endsection
