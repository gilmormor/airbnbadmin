@extends('layouts.app')

@section('page-title', 'Nuevo ítem de menú')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('menus.store') }}">
                @csrf
                @include('admin.menus._form')
            </form>
        </div>
    </div>
@endsection
