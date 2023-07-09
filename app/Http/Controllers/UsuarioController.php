<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use App\Models\Audit;
use Illuminate\Support\Facades\Auth;


class UsuarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver-usuario|crear-usuario|editar-usuario|borrar-usuario')->only('index');
        $this->middleware('permission:crear-usuario')->only(['create', 'store']);
        $this->middleware('permission:editar-usuario')->only(['edit', 'update']);
        $this->middleware('permission:borrar-usuario')->only('destroy');
    }

    public function index()
    {
        $usuarios = User::paginate(5);
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        return view('usuarios.crear', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required',
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        $this->createAudit('create', null, $user);

        return redirect()->route('usuarios.index');
    }

    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();

        return view('usuarios.editar', compact('user', 'roles', 'userRole'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|same:confirm-password',
            'roles' => 'required',
        ]);

        $input = $request->all();

        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, ['password']);
        }

        $user = User::find($id);
        $oldData = $user->toArray();
        $user->update($input);

        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->assignRole($request->input('roles'));

        $this->createAudit('update', $oldData, $user);

        return redirect()->route('usuarios.index');
    }

    public function destroy($id)
    {
        $user = User::find($id);
    
        if (!$user) {
            // El usuario no existe, manejar el escenario adecuado aquí
            // Por ejemplo, redirigir a una página de error o mostrar un mensaje de error
        } else {
            $oldData = $user->toArray();
    
            $user->delete();
    
            $this->createAudit('delete', $oldData);
        }
    
        return redirect()->route('usuarios.index');
    }
    

    private function createAudit($action, $oldData = null, $newData = null)
    {
        $audit = new Audit();
        $audit->user_id = Auth::user()->id;
        $audit->action = $action;
        $audit->old_data = json_encode($oldData);
        $audit->new_data = json_encode($newData);
        $audit->save();
    }
}