<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UsuarioRequest;
use App\Models\Propietario;
use App\Models\User;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::with(['roles', 'propietario'])->orderBy('name')->get();

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('admin.usuarios.create', $this->formData());
    }

    public function store(UsuarioRequest $request)
    {
        $data = $request->validated();

        $usuario = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'propietario_id' => $data['role'] === 'Propietario' ? $data['propietario_id'] : null,
        ]);

        $usuario->syncRoles([$data['role']]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        return view('admin.usuarios.edit', array_merge(['usuario' => $usuario], $this->formData()));
    }

    public function update(UsuarioRequest $request, User $usuario)
    {
        $data = $request->validated();

        $usuario->name = $data['name'];
        $usuario->email = $data['email'];
        $usuario->propietario_id = $data['role'] === 'Propietario' ? $data['propietario_id'] : null;

        if (! empty($data['password'])) {
            $usuario->password = $data['password'];
        }

        $usuario->save();
        $usuario->syncRoles([$data['role']]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return redirect()->route('usuarios.index')->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }

    private function formData(): array
    {
        return [
            'propietarios' => Propietario::orderBy('nombre')->get(),
        ];
    }
}
