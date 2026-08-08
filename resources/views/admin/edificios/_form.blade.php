@include('partials.form-errors')

<div class="mb-3">
    <label class="form-label">Nombre</label>
    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $edificio->nombre ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Dirección</label>
    <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $edificio->direccion ?? '') }}">
</div>

<button type="submit" class="btn btn-primary">Guardar</button>
<a href="{{ route('edificios.index') }}" class="btn btn-secondary">Cancelar</a>
