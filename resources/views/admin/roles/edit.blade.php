@extends('layouts.app')

@section('page-title', 'Editar rol')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('roles.update', $rol) }}">
                @csrf
                @method('PUT')
                @include('admin.roles._form')
            </form>
        </div>
    </div>
@endsection
