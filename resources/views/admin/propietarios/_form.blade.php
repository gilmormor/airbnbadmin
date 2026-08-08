@include('partials.form-errors')

<div class="mb-3">
    <label class="form-label">Nombre</label>
    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $propietario->nombre ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $propietario->email ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Teléfono</label>
    <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $propietario->telefono ?? '') }}">
</div>

<button type="submit" class="btn btn-primary">Guardar</button>
<a href="{{ route('propietarios.index') }}" class="btn btn-secondary">Cancelar</a>
