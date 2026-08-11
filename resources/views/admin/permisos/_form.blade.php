@include('partials.form-errors')

<div class="mb-3">
    <label class="form-label">Nombre del permiso</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $permiso->name ?? '') }}" placeholder="ej. reservas.ver" required>
    <div class="form-text">Este nombre es el que se usa internamente para proteger una pantalla o acción. Crear el permiso aquí no protege nada por sí solo; debe estar conectado en el código a la ruta correspondiente.</div>
</div>

<button type="submit" class="btn btn-primary">Guardar</button>
<a href="{{ route('permisos.index') }}" class="btn btn-secondary">Cancelar</a>
