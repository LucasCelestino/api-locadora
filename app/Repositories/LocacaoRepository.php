<?php

namespace App\Repositories;

use App\Models\Locacao;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LocacaoRepository implements AbstractRepository
{
    private Locacao $locacao;

    public function __construct(Locacao $locacao)
    {
        $this->locacao = $locacao;
    }

    public function findAll(): LengthAwarePaginator
    {
        return $this->locacao->paginate();
    }

    public function findById(int $id)
    {
        return $this->locacao->with('cliente')->with('carro')->find($id);
    }

    public function save(array $data)
    {
        if(array_key_exists('id', $data))
        {
            $locacao = $this->findById($data['id']);

            $locacao->fill($data);

            $locacao->save();

            return $locacao;
        }
        else
        {
            return $this->locacao->create($data);
        }
    }

    public function delete($entity): bool
    {
        if(!$entity->delete())
        {
            return false;
        }

        return true;
    }
}
