@extends('layouts.app')

@section('page-title', 'Editar sucursal')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('sucursales.update', $sucursal) }}"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.sucursales._form')
            </form>
        </div>
    </div>

    {{-- Va fuera del formulario principal: HTML no admite formularios anidados. --}}
    @include('partials.galeria', ['modelo' => $sucursal, 'tipo' => 'sucursal'])
@endsection
