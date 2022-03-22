<?php


namespace App\Repositories\Classes;

use App\Models\Parameter;
use App\Repositories\Interfaces\ParameterRepositoryInterface;

class ParameterRepository implements ParameterRepositoryInterface
{

    /**
     * @param Parameter $parameter
     * @return mixed
     */
    public function save(Parameter $parameter)
    {
         $parameter->save();
         return $parameter;
    }

    /**
     * @return mixed
     */
    public function newParameterObject()
    {
        return new Parameter();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return Parameter::findOrFail($id);
    }

    /**
     * @param Parameter $parameter
     * @return mixed
     */
    public function delete(Parameter $parameter)
    {
        return $parameter->delete();
    }
}
