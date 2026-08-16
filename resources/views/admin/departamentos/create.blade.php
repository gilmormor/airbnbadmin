@extends('layouts.app')

@section('page-title', 'Nuevo departamento')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('departamentos.store') }}" enctype="multipart/form-data">
                @csrf
                @include('admin.departamentos._form')
            </form>
        </div>
    </div>
@endsection
