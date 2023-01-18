<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AbstractRepository
{
    public function save(array $request);

    public function findAll(): LengthAwarePaginator;

    public function findById(int $id);

    public function delete($entity);
}
