<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Cliente;

use App\Models\Audit;


class ClienteController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:ver-cliente|crear-cliente|editar-cliente|borrar-cliente')->only('index');
        $this->middleware('permission:crear-cliente', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-cliente', ['only' => ['edit', 'update']]);
        $this->middleware('permission:borrar-cliente', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Con paginación
        $clientes = Cliente::paginate(5);
        return view('clientes.index', compact('clientes'));
        //al usar esta paginacion, recordar poner en el el index.blade.php este codigo  {!! $clientes->links() !!}    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('clientes.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'cedula' => 'required',
            'nombre' => 'required',
            'apellido' => 'required',
            'correo' => 'required',
            'telefono' => 'required',
        ]);

        $cliente = Cliente::create($request->all());

        $this->createAudit('create', null, json_encode($cliente->toArray()));

        return redirect()->route('clientes.index');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Cliente $cliente)
    {
        return view('clientes.editar', compact('cliente'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cliente $cliente)
    {
        request()->validate([
            'cedula' => 'required',
            'nombre' => 'required',
            'apellido' => 'required',
            'correo' => 'required',
            'telefono' => 'required',
        ]);

        $oldData = json_encode($cliente->toArray());

        $cliente->update($request->all());

        $this->createAudit('update', $oldData, json_encode($cliente->toArray()));

        return redirect()->route('clientes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cliente $cliente)
    {
        $oldData = json_encode($cliente->toArray());

        $cliente->delete();

        $this->createAudit('delete', $oldData);

        return redirect()->route('clientes.index');
    }
    private function createAudit($action, $oldData = null, $newData = null)
    {
        $audit = new Audit();
        $audit->user_id = auth()->user()->id;
        $audit->action = $action;
        $audit->old_data = $oldData;
        $audit->new_data = $newData;
        $audit->save();
    }
}