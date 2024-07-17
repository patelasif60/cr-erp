<?php

namespace App\Services;

use App\Repositories\MaterialTypeRepository;

/**
 * supplier class to handle operator interactions.
 */

class MaterialTypeService
{
    public function __construct(MaterialTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->getAll();
    }
    public function create($data)
    {
        return $this->repository->create($data);
    }
    public function destroy($data)
    {
        return $this->repository->destroy($data);
    }
    public function update($data)
    {
        return $this->repository->update($data);
    }
}