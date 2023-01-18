<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Http\Requests\UpdateClienteRequest;
use App\Repositories\ClienteRepository;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    private Cliente $cliente;
    private ClienteRepository $clienteRepository;

    public function __construct(Cliente $cliente, ClienteRepository $clienteRepository)
    {
        $this->cliente = $cliente;
        $this->clienteRepository = $clienteRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clientes = $this->clienteRepository->findAll();

        return response()->json($clientes, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreClienteRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->cliente->rules(), $this->cliente->feedback());

        $cliente = $this->clienteRepository->save([
            'nome'=>$request->nome
        ]);

        return response()->json($cliente, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $cliente = $this->clienteRepository->findById($id);

        if($cliente === null)
        {
            return response()->json(['mensagem'=>[
                'erro'=>'Recurso pesquisado não existe.'
            ]], 404);
        }

        return response()->json($cliente, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateClienteRequest  $request
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $request->validate($this->cliente->rules(), $this->cliente->feedback());

        $cliente = $this->clienteRepository->findById($id);

        if($cliente === null)
        {
            return response()->json(['mensagem'=>[
                'erro'=>'Recurso pesquisado não existe.'
            ]], 404);
        }

        $cliente->nome = $request->nome;

        $updatedCliente = $this->clienteRepository->save($cliente->getAttributes());

        return response()->json($updatedCliente, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $cliente = $this->clienteRepository->findById($id);

        if($cliente === null)
        {
            return response()->json(['mensagem'=>[
                'erro'=>'Recurso pesquisado não existe.'
            ]], 404);
        }

        if(!$this->clienteRepository->delete($cliente))
        {
            return response()->json(['mensagem'=>['erro'=>'Ocorreu um erro ao remover o recurso solicitado, tente novamente.']], 500);
        }

        return response()->json(['mensagem'=>[
            'sucesso'=>'Recurso removido com sucesso.'
        ]], 200);
    }
}
