@include('partials.form-errors')

<div class="mb-3">
    <label class="form-label">Edificio</label>
    <select name="edificio_id" class="form-select" required>
        <option value="">Seleccione...</option>
        @foreach ($edificios as $edificio)
            <option value="{{ $edificio->id }}" @selected(old('edificio_id', $departamento->edificio_id ?? '') == $edificio->id)>
                {{ $edificio->nombre }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Propietario</label>
    <select name="propietario_id" class="form-select" required>
        <option value="">Seleccione...</option>
        @foreach ($propietarios as $propietario)
            <option value="{{ $propietario->id }}" @selected(old('propietario_id', $departamento->propietario_id ?? '') == $propietario->id)>
                {{ $propietario->nombre }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Nombre / número de departamento</label>
    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $departamento->nombre ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">% Comisión de coanfitrión</label>
    <input type="number" step="0.01" min="0" max="100" name="comision_coanfitrion_pct" class="form-control" value="{{ old('comision_coanfitrion_pct', $departamento->comision_coanfitrion_pct ?? '') }}">
    <div class="form-text">Porcentaje que se descuenta al propietario por gestión de coanfitrión. Se aplica a cada reserva importada.</div>
</div>


<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Beds24 - Property ID</label>
        <input type="number" name="beds24_prop_id" class="form-control" value="{{ old('beds24_prop_id', $departamento->beds24_prop_id ?? '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Beds24 - Room ID (opcional)</label>
        <input type="number" name="beds24_room_id" class="form-control" value="{{ old('beds24_room_id', $departamento->beds24_room_id ?? '') }}">
    </div>
    <div class="form-text mb-3">
        Necesario para que la sincronización automática con Beds24 asigne correctamente cada reserva a este departamento.
        Consulta <a href="{{ route('beds24.propiedades') }}" target="_blank">el listado de propiedades de tu cuenta Beds24</a> para obtener estos IDs.
    </div>
</div>

<button type="submit" class="btn btn-primary">Guardar</button>
<a href="{{ route('departamentos.index') }}" class="btn btn-secondary">Cancelar</a>
