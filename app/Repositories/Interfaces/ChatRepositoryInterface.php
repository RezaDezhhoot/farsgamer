<?php

namespace App\Repositories\Interfaces;

use App\Models\ChatGroup;

interface ChatRepositoryInterface
{
    public function startChat($id);

    public function contacts();

    public function singleContact($id);

    public function sendMessage( array $data);

    public function getAllAdminListGroup($search);

    public function getStatus();

    public function find($id);

    public function closeStatus();

    public function openStatus();

    public function save(ChatGroup $chatGroup);

    public function delete(ChatGroup $chatGroup);
}
