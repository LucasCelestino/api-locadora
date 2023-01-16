<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarcaController extends Controller
{
    private Marca $marca;
    private MarcaRepository $marcaRepository;

    public function __construct(Marca $marca, MarcaRepository $marcaRepository)
    {
        $this->marca = $marca;
        $this->marcaRepository = $marcaRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $marcas = $this->marcaRepository->findAll();

        return response()->json($marcas, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->marca->rules(), $this->marca->feedback());

        $image = $request->file('imagem');

        $image_urn = $image->store('imagens', 'public');

        $marca = $this->marcaRepository->save([
            'nome'=>$request->nome,
            'imagem'=>$image_urn
        ]);

        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $marca = $this->marcaRepository->findById($id);

        if($marca === null)
        {
            return response()->json(['mensagem'=>[
                'erro'=>'Recurso pesquisado não existe.'
            ]], 404);
        }

        return response()->json($marca, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $marca = $this->marcaRepository->findById($id);

        if($marca === null)
        {
            return response()->json(['mensagem'=>[
                'erro'=>'Recurso pesquisado não existe.'
            ]], 404);
        }

        if($request->method() === 'PATCH')
        {
            $dynamicRules = array();

            foreach($marca->rules() as $input => $rule)
            {
                if(array_key_exists($input, $request->all()))
                {
                    $dynamicRules[$input] = $rule;
                }
            }

            $request->validate($dynamicRules, $marca->feedback());

            if($request->file('imagem'))
            {
                Storage::disk('public')->delete($marca->imagem);

                $image = $request->file('imagem');

                $image_urn = $image->store('imagens', 'public');

                $marca->imagem = $image_urn;

                $updatedMarca = $this->marcaRepository->save($marca->getAttributes());
            }
            else
            {
                $marca->nome = $request->nome;

                $updatedMarca = $this->marcaRepository->save($marca->getAttributes());
            }
        }
        else
        {
            $request->validate($marca->rules(), $marca->feedback());

            Storage::disk('public')->delete($marca->imagem);

            $image = $request->file('imagem');

            $image_urn = $image->store('imagens', 'public');

            $marca->imagem = $image_urn;
            $marca->nome = $request->nome;

            $updatedMarca = $this->marcaRepository->save($marca->getAttributes());
        }

        return response()->json($updatedMarca, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $marca = $this->marcaRepository->findById($id);

        if($marca === null)
        {
            return response()->json(['mensagem'=>[
                'erro'=>'Recurso pesquisado não existe.'
            ]], 404);
        }

        Storage::disk('public')->delete($marca->imagem);

        if(!$this->marcaRepository->delete($marca))
        {
            return response()->json(['mensagem'=>['erro'=>'Ocorreu um erro ao remover o recurso solicitado, tente novamente.']], 500);
        }

        return response()->json(['mensagem'=>[
            'sucesso'=>'Recurso removido com sucesso.'
        ]], 200);
    }
}
