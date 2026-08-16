@include('partials.form-errors')

@php
    $e = $edificio ?? null;
    $valor = fn ($campo, $defecto = '') => old($campo, $e->$campo ?? $defecto);
@endphp

<p class="text-secondary small">
    Un edificio es una construcción física dentro de una sucursal. La marca, la
    ubicación y el contacto se administran en la sucursal, no aquí.
</p>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Sucursal</label>
        <select name="sucursal_id" class="form-select" required>
            <option value="">Seleccione...</option>
            @foreach ($sucursales as $sucursal)
                <option value="{{ $sucursal->id }}" @selected($valor('sucursal_id') == $sucursal->id)>
                    {{ $sucursal->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" value="{{ $valor('nombre') }}" required>
        <div class="form-text">Ejemplo: Bloque A, Bloque B.</div>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-3">
        <label class="form-label">Cantidad de pisos</label>
        <input type="number" name="pisos" class="form-control" min="1" max="200" value="{{ $valor('pisos') }}">
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Orden</label>
        <input type="number" name="orden" class="form-control" min="0" value="{{ $valor('orden', 0) }}" required>
    </div>
</div>

<hr class="my-4">

<button type="submit" class="btn btn-primary">Guardar</button>
<a href="{{ route('edificios.index') }}" class="btn btn-secondary">Cancelar</a>
