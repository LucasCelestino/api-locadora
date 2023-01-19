<?php

namespace App\Http\Controllers;

use App\Models\Carro;
use App\Http\Requests\StoreCarroRequest;
use App\Http\Requests\UpdateCarroRequest;
use App\Repositories\CarroRepository;
use Illuminate\Http\Request;

class CarroController extends Controller
{
    private Carro $carro;
    private CarroRepository $carroRepository;

    public function __construct(Carro $carro, CarroRepository $carroRepository)
    {
        $this->carro = $carro;
        $this->carroRepository = $carroRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $carros = $this->carroRepository->findAll();

        return response()->json($carros, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCarroRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->carro->rules(), $this->carro->feedback());

        $carro = $this->carroRepository->save($request->all());

        return response()->json($carro, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $carro = $this->carroRepository->findById($id);

        if($carro === null)
        {
            return response()->json(['mensagem'=>[
                'erro'=>'Recurso pesquisado não existe.'
            ]], 404);
        }

        return response()->json($carro, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCarroRequest  $request
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $carro = $this->carroRepository->findById($id);

        if($carro === null)
        {
            return response()->json(['mensagem'=>[
                'erro'=>'Recurso pesquisado não existe.'
            ]], 404);
        }

        if($request->method() === 'PATCH')
        {
            $dynamicRules = array();

            foreach($carro->rules() as $input => $rule)
            {
                if(array_key_exists($input, $request->all()))
                {
                    $dynamicRules[$input] = $rule;
                }
            }

            $request->validate($dynamicRules);

            foreach ($request->all() as $key => $value)
            {
                $carro->$key = $value;
            }
        }
        else
        {
            $request->validate($this->carro->rules(), $this->carro->feedback());

            foreach ($request->all() as $key => $value)
            {
                $carro->$key = $value;
            }
        }

        $updatedCarro = $this->carroRepository->save($carro->getAttributes());

        return response()->json($updatedCarro, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $carro = $this->carroRepository->findById($id);

        if($carro === null)
        {
            return response()->json(['mensagem'=>[
                'erro'=>'Recurso pesquisado não existe.'
            ]], 404);
        }

        if(!$this->carroRepository->delete($carro))
        {
            return response()->json(['mensagem'=>['erro'=>'Ocorreu um erro ao remover o recurso solicitado, tente novamente.']], 500);
        }

        return response()->json(['mensagem'=>[
            'sucesso'=>'Recurso removido com sucesso.'
        ]], 200);
    }
}
