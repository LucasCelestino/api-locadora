<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarcaController extends Controller
{
    private Marca $marca;

    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $marcas = $this->marca->all();

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

        $marca = $this->marca->create([
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
        $marca = $this->marca->find($id);

        if($marca === null)
        {
            return response()->json(['erro'=>'Recurso pesquisado não existe.'], 404);
        }

        return $marca;
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
        $marca = $this->marca->find($id);

        if($marca === null)
        {
            return response()->json(['erro'=>'Recurso pesquisado não existe.'], 404);
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
        }
        else
        {
            $request->validate($marca->rules(), $marca->feedback());
        }

        if($request->file('imagem'))
        {
            Storage::disk('public')->delete($marca->imagem);
        }

        $image = $request->file('imagem');

        $image_urn = $image->store('imagens', 'public');

        $marca->update([
            'nome'=>$request->nome,
            'imagem'=>$image_urn
        ]);

        return $marca;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $marca = $this->marca->find($id);

        if($marca === null)
        {
            return response()->json(['erro'=>'Recurso pesquisado não existe.'], 404);
        }

        Storage::disk('public')->delete($marca->imagem);

        $marca->delete();

        return ['msg'=>'Marca removida com sucesso.'];
    }
}
