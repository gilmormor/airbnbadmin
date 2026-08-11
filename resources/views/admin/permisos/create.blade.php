@extends('layouts.app')

@section('page-title', 'Nuevo permiso')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('permisos.store') }}">
                @csrf
                @include('admin.permisos._form')
            </form>
        </div>
    </div>
@endsection
