@extends('layouts.app')

@section('page-title', 'Nuevo propietario')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('propietarios.store') }}">
                @csrf
                @include('admin.propietarios._form')
            </form>
        </div>
    </div>
@endsection
