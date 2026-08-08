@extends('layouts.app')

@section('page-title', 'Nuevo edificio')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('edificios.store') }}">
                @csrf
                @include('admin.edificios._form')
            </form>
        </div>
    </div>
@endsection
