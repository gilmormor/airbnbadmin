@extends('layouts.app')

@section('page-title', 'Nueva sucursal')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('sucursales.store') }}" enctype="multipart/form-data">
                @csrf
                @include('admin.sucursales._form')
            </form>
        </div>
    </div>
@endsection
