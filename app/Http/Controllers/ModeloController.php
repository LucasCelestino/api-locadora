<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModeloController extends Controller
{
    private Modelo $modelo;

    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $modelos = '';

        if($request->has('filters'))
        {
            $filters = explode(';', $request->filters);

            foreach ($filters as $key => $value)
            {
                $filter = explode(':', $value);
                $modelos = $this->modelo->where($filter[0], $filter[1], $filter[2]);
            }
        }
        else
        {
            $modelos = $this->modelo;
        }

        if($request->has('fields'))
        {
            $attributes = $request->fields;

            $modelos = $modelos->selectRaw($attributes)->with('marca')->get();
        }
        else
        {
            $modelos = $modelos->with('marca')->get();
        }

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

        $modelo = $this->modelo->create([
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
        $modelo = $this->modelo->with('marca')->find($id);

        if($modelo === null)
        {
            return response()->json(['erro'=>'Recurso pesquisado não existe.'], 404);
        }

        return $modelo;
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
        $modelo = $this->modelo->with('marca')->find($id);

        if($modelo === null)
        {
            return response()->json(['erro'=>'Recurso pesquisado não existe.'], 404);
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
        }
        else
        {
            $request->validate($modelo->rules());
        }

        if($request->file('imagem'))
        {
            Storage::disk('public')->delete($modelo->imagem);

            $image = $request->file('imagem');

            $image_urn = $image->store('imagens/modelos', 'public');

            $modelo->fill($request->all());

            $modelo->imagem = $image_urn;
        }
        else
        {
            $modelo->fill($request->all());
        }

        $modelo->save();

        return $modelo;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $modelo = $this->modelo->with('marca')->find($id);

        if($modelo === null)
        {
            return response()->json(['erro'=>'Recurso pesquisado não existe.'], 404);
        }

        Storage::disk('public')->delete($modelo->imagem);

        $modelo->delete();

        return ['msg'=>'Modelo removido com sucesso.'];
    }
}
