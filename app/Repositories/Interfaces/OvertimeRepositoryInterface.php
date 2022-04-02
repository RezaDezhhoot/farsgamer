<?php

namespace App\Repositories\Interfaces;

use App\Models\Overtime;

interface OvertimeRepositoryInterface
{
    public function find($id);

    public function delete(Overtime $overtime);

    public function create(array $data);
}
