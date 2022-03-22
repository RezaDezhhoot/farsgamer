<?php

namespace App\Repositories\Interfaces;


use App\Models\Parameter;

interface ParameterRepositoryInterface
{
    public function save(Parameter $parameter);

    public function newParameterObject();

    public function find($id);

    public function delete(Parameter $parameter);
}
