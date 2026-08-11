@include('partials.form-errors')

<div class="mb-3">
    <label class="form-label">Nombre del rol</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $rol->name ?? '') }}" required>
</div>

<button type="submit" class="btn btn-primary">Guardar</button>
<a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancelar</a>
