@include('partials.form-errors')

@php
    $rolActual = old('role', isset($usuario) ? ($usuario->roles->first()->name ?? '') : '');
@endphp

<div class="mb-3">
    <label class="form-label">Nombre</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $usuario->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $usuario->email ?? '') }}" required>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Contraseña {{ isset($usuario) ? '(dejar en blanco para no cambiarla)' : '' }}</label>
        <input type="password" name="password" class="form-control" {{ isset($usuario) ? '' : 'required' }}>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Confirmar contraseña</label>
        <input type="password" name="password_confirmation" class="form-control">
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Rol</label>
    <select name="role" id="campo-rol" class="form-select" required>
        <option value="Administrador" @selected($rolActual === 'Administrador')>Administrador</option>
        <option value="Propietario" @selected($rolActual === 'Propietario')>Propietario</option>
    </select>
</div>

<div class="mb-3" id="campo-propietario" style="{{ $rolActual === 'Propietario' ? '' : 'display:none' }}">
    <label class="form-label">Propietario vinculado</label>
    <select name="propietario_id" class="form-select">
        <option value="">Seleccione...</option>
        @foreach ($propietarios as $propietario)
            <option value="{{ $propietario->id }}" @selected(old('propietario_id', $usuario->propietario_id ?? '') == $propietario->id)>
                {{ $propietario->nombre }}
            </option>
        @endforeach
    </select>
</div>

<button type="submit" class="btn btn-primary">Guardar</button>
<a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>

@push('scripts')
    <script>
        document.getElementById('campo-rol').addEventListener('change', function () {
            document.getElementById('campo-propietario').style.display = this.value === 'Propietario' ? '' : 'none';
        });
    </script>
@endpush
