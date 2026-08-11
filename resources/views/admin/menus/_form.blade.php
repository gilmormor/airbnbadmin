@include('partials.form-errors')

<div class="mb-3">
    <label class="form-label">Menú padre (opcional)</label>
    <select name="menu_id" class="form-select">
        <option value="">— Ninguno (nivel raíz) —</option>
        @foreach ($menusPadre as $padre)
            <option value="{{ $padre->id }}" @selected(old('menu_id', $menu->menu_id ?? '') == $padre->id)>
                {{ $padre->nombre }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Nombre</label>
    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $menu->nombre ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Ruta (nombre de ruta de Laravel, ej. <code>dashboard</code>)</label>
    <input type="text" name="ruta" class="form-control" value="{{ old('ruta', $menu->ruta ?? '') }}">
    <div class="form-text">Déjalo en blanco si este ítem es solo un agrupador (no enlaza a ninguna pantalla).</div>
</div>

<div class="mb-3">
    <label class="form-label">Ícono (clase de Bootstrap Icons, ej. <code>bi bi-speedometer2</code>)</label>
    <div class="input-group">
        <span class="input-group-text" id="icono-preview">
            <i class="{{ old('icono', $menu->icono ?? '') ?: 'bi bi-question-lg' }}"></i>
        </span>
        <input type="text" name="icono" id="icono-input" class="form-control" value="{{ old('icono', $menu->icono ?? '') }}" placeholder="bi bi-speedometer2">
    </div>
    <div class="form-text">Puedes buscar el nombre exacto en <a href="https://icons.getbootstrap.com" target="_blank" rel="noopener">icons.getbootstrap.com</a>.</div>
</div>

<div class="mb-3">
    <label class="form-label">Orden</label>
    <input type="number" name="orden" class="form-control" min="0" value="{{ old('orden', $menu->orden ?? 0) }}">
</div>

<button type="submit" class="btn btn-primary">Guardar</button>
<a href="{{ route('menus.index') }}" class="btn btn-secondary">Cancelar</a>

@push('scripts')
    <script>
        document.getElementById('icono-input')?.addEventListener('input', function () {
            document.querySelector('#icono-preview i').className = this.value.trim() || 'bi bi-question-lg';
        });
    </script>
@endpush
