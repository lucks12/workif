<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // select * from tb_produtos order by id desc limit 10
        $users = User::orderBy("id", "desc")->paginate(10);
        return view("users.index", compact("users"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("users.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $request->validate([
            'nome' => 'required|min:3',
            'email' => 'required|min:3',
            'password' => 'required|min:3',
            'siape' => 'required|max:50',
            'carga_horaria_semanal' => 'required|numeric',
            'funcao' => 'required|max:100',
            'cargo_efetivo' => 'required|max:100',
        ]);

        $user = new User();
        $user->fill($request->all());
        $user->gerenciar =  $request->gerenciar == 1 ? 1 : 0;
        $user->role = "ROLE_USER";
        $user->save();


        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view("users.show", compact("user"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view("users.edit", compact("user"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $request->validate([
            'nome' => 'required|min:3',
            'email' => 'required|min:3',
            'siape' => 'required|max:50',
            'carga_horaria_semanal' => 'required|numeric',
            'funcao' => 'required|max:100',
            'cargo_efetivo' => 'required|max:100',
        ]);


            $user = User::find($id);
            $user->fill($request->all());
            $user->gerenciar = $request->gerenciar == 1 ? 1 : 0;
            $user->save();

        return redirect($request->input('previous_url', route('users.index')))->with('success', 'Usuário atualizado com sucesso!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect('/users')->with('completed', 'Usuario removido com sucesso');
    }
}