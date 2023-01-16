<?php

namespace App\Repositories;

use App\Models\Marca;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MarcaRepository implements AbstractRepository
{
    private Marca $marca;

    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }

    public function findAll(): LengthAwarePaginator
    {
        return $this->marca->paginate();
    }

    public function findById(int $id)
    {
        return $this->marca->with('modelos')->find($id);
    }

    public function save(array $data)
    {
        if(array_key_exists('id', $data))
        {
            $marca = $this->findById($data['id']);

            $marca->fill($data);

            $marca->save();

            return $marca;
        }
        else
        {
            return $this->marca->create($data);
        }
    }

    public function delete(Marca $entity): bool
    {
        if(!$entity->delete())
        {
            return false;
        }

        return true;
    }
}
