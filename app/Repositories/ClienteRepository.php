<?php

namespace App\Repositories;

use App\Models\Cliente;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClienteRepository implements AbstractRepository
{
    private Cliente $cliente;

    public function __construct(Cliente $cliente)
    {
        $this->cliente = $cliente;
    }

    public function findAll(): LengthAwarePaginator
    {
        return $this->cliente->paginate();
    }

    public function findById(int $id)
    {
        return $this->cliente->find($id);
    }

    public function save(array $data)
    {
        if(array_key_exists('id', $data))
        {
            $cliente = $this->findById($data['id']);

            $cliente->fill($data);

            $cliente->save();

            return $cliente;
        }
        else
        {
            return $this->cliente->create($data);
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
