@extends('layouts.app')

@section('page-title', 'Editar ítem de menú')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('menus.update', $menu) }}">
                @csrf
                @method('PUT')
                @include('admin.menus._form')
            </form>
        </div>
    </div>
@endsection
