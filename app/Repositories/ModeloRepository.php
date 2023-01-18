<?php

namespace App\Repositories;

use App\Models\Modelo;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ModeloRepository implements AbstractRepository
{
    private Modelo $modelo;

    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }

    public function findAll(): LengthAwarePaginator
    {
        return $this->modelo->paginate();
    }

    public function findById(int $id)
    {
        return $this->modelo->with('marca')->find($id);
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
            return $this->modelo->create($data);
        }
    }

    public function delete($entity)
    {
        if(!$entity->delete())
        {
            return false;
        }

        return true;
    }
}
