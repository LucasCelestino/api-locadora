<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use App\Repositories\ModeloRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModeloController extends Controller
{
    private Modelo $modelo;
    private ModeloRepository $modeloRepository;

    public function __construct(Modelo $modelo, ModeloRepository $modeloRepository)
    {
        $this->modelo = $modelo;
        $this->modeloRepository = $modeloRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $modelos = $this->modeloRepository->findAll();

        return response()->json($modelos, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->modelo->rules());

        $image = $request->file('imagem');

        $image_urn = $image->store('imagens/modelos', 'public');

        $modelo = $this->modeloRepository->save([
            'marca_id'=>$request->marca_id,
            'nome'=>$request->nome,
            'imagem'=>$image_urn,
            'numero_portas'=>$request->numero_portas,
            'lugares'=>$request->lugares,
            'air_bag'=>$request->air_bag,
            'abs'=>$request->abs
        ]);

        return response()->json($modelo, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $modelo = $this->modeloRepository->findById($id);

        if($modelo === null)
        {
            return response()->json(['mensagem'=>[
                'erro'=>'Recurso pesquisado não existe.'
            ]], 404);
        }

        return response()->json($modelo, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $modelo = $this->modeloRepository->findById($id);

        if($modelo === null)
        {
            return response()->json(['mensagem'=>[
                'erro'=>'Recurso pesquisado não existe.'
            ]], 404);
        }

        if($request->method() === 'PATCH')
        {
            $dynamicRules = array();

            foreach($modelo->rules() as $input => $rule)
            {
                if(array_key_exists($input, $request->all()))
                {
                    $dynamicRules[$input] = $rule;
                }
            }

            $request->validate($dynamicRules);

            if($request->file('imagem'))
            {
                Storage::disk('public')->delete($modelo->imagem);

                $image = $request->file('imagem');

                $image_urn = $image->store('imagens', 'public');

                $modelo->imagem = $image_urn;

                foreach ($request->all() as $key => $value)
                {
                    if($key == 'imagem')
                    {
                        continue;
                    }

                    $modelo->$key = $value;
                }

                $updatedMarca = $this->modeloRepository->save($modelo->getAttributes());
            }
            else
            {
                dd($request->all());
                $updatedMarca = $this->modeloRepository->save($modelo->getAttributes());
            }
        }
        else
        {
            $request->validate($modelo->rules());

            Storage::disk('public')->delete($modelo->imagem);

            $image = $request->file('imagem');

            $image_urn = $image->store('imagens', 'public');

            $modelo->imagem = $image_urn;

            foreach ($request->all() as $key => $value)
            {
                if($key == 'imagem')
                {
                    continue;
                }

                $modelo->$key = $value;
            }

            $updatedMarca = $this->modeloRepository->save($modelo->getAttributes());
        }

        return response()->json($updatedMarca, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $modelo = $this->modeloRepository->findById($id);

        if($modelo === null)
        {
            return response()->json(['mensagem'=>[
                'erro'=>'Recurso pesquisado não existe.'
            ]], 404);
        }

        Storage::disk('public')->delete($modelo->imagem);

        if(!$this->modeloRepository->delete($modelo))
        {
            return response()->json(['mensagem'=>['erro'=>'Ocorreu um erro ao remover o recurso solicitado, tente novamente.']], 500);
        }

        return response()->json(['mensagem'=>[
            'sucesso'=>'Recurso removido com sucesso.'
        ]], 200);
    }
}
