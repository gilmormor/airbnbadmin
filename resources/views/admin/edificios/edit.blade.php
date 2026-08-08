@extends('layouts.app')

@section('page-title', 'Editar edificio')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('edificios.update', $edificio) }}">
                @csrf
                @method('PUT')
                @include('admin.edificios._form')
            </form>
        </div>
    </div>
@endsection
