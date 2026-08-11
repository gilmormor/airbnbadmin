@extends('layouts.app')

@section('page-title', 'Nuevo rol')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('roles.store') }}">
                @csrf
                @include('admin.roles._form')
            </form>
        </div>
    </div>
@endsection
