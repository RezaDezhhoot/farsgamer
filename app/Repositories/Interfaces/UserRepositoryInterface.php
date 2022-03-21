<?php
namespace App\Repositories\Interfaces;
use App\Models\User;

interface UserRepositoryInterface
{
    public function getUser($col,$value);

    public function find($id);

    public function create(array $data);

    public function update(User $user, array $data);

    public function getStatus();

    public function getMyOrders(User $user , $active = true);

    public function getMyComments(User $user , $active = true);
}
