<?php

namespace App\Repositories;

use App\Models\Carro;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CarroRepository implements AbstractRepository
{
    private Carro $carro;

    public function __construct(Carro $carro)
    {
        $this->carro = $carro;
    }

    public function findAll(): LengthAwarePaginator
    {
        return $this->carro->with('modelo')->paginate();
    }

    public function findById(int $id)
    {
        return $this->carro->with('modelo')->find($id);
    }

    public function save(array $data)
    {
        if(array_key_exists('id', $data))
        {
            $carro = $this->findById($data['id']);

            $carro->fill($data);

            $carro->save();

            return $carro;
        }
        else
        {
            return $this->carro->create($data);
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
