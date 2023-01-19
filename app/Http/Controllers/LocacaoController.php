<?php

namespace App\Http\Controllers;

use App\Models\Locacao;
use App\Http\Requests\StoreLocacaoRequest;
use App\Http\Requests\UpdateLocacaoRequest;
use App\Repositories\LocacaoRepository;
use Illuminate\Http\Request;

class LocacaoController extends Controller
{
    private Locacao $locacao;
    private LocacaoRepository $locacaoRepository;

    public function __construct(Locacao $locacao, LocacaoRepository $locacaoRepository)
    {
        $this->locacao = $locacao;
        $this->locacaoRepository = $locacaoRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $locacoes = $this->locacaoRepository->findAll();

        return response()->json($locacoes, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreLocacaoRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->locacao->rules(), $this->locacao->feedback());

        $locacao = $this->locacaoRepository->save($request->all());

        return response()->json($locacao, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Locacao  $locacao
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $locacao = $this->locacaoRepository->findById($id);

        if($locacao === null)
        {
            return response()->json(['mensagem'=>[
                'erro'=>'Recurso pesquisado não existe.'
            ]], 404);
        }

        return response()->json($locacao, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLocacaoRequest  $request
     * @param  \App\Models\Locacao  $locacao
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $locacao = $this->locacaoRepository->findById($id);

        if($locacao === null)
        {
            return response()->json(['mensagem'=>[
                'erro'=>'Recurso pesquisado não existe.'
            ]], 404);
        }

        if($request->method() === 'PATCH')
        {
            $dynamicRules = array();

            foreach($locacao->rules() as $input => $rule)
            {
                if(array_key_exists($input, $request->all()))
                {
                    $dynamicRules[$input] = $rule;
                }
            }

            $request->validate($dynamicRules);

            foreach ($request->all() as $key => $value)
            {
                $locacao->$key = $value;
            }
        }
        else
        {
            $request->validate($this->locacao->rules(), $this->locacao->feedback());

            foreach ($request->all() as $key => $value)
            {
                $locacao->$key = $value;
            }
        }

        $updatedLocacao = $this->locacaoRepository->save($locacao->getAttributes());

        return response()->json($updatedLocacao, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Locacao  $locacao
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $locacao = $this->locacaoRepository->findById($id);

        if($locacao === null)
        {
            return response()->json(['mensagem'=>[
                'erro'=>'Recurso pesquisado não existe.'
            ]], 404);
        }

        if(!$this->locacaoRepository->delete($locacao))
        {
            return response()->json(['mensagem'=>['erro'=>'Ocorreu um erro ao remover o recurso solicitado, tente novamente.']], 500);
        }

        return response()->json(['mensagem'=>[
            'sucesso'=>'Recurso removido com sucesso.'
        ]], 200);
    }
}
