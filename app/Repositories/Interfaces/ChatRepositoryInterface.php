<?php

namespace App\Repositories\Interfaces;

use App\Http\Resources\v1\Group;
use App\Models\ChatGroup;

interface ChatRepositoryInterface
{
    public function startChat($id);

    public function contacts();

    public function get($model);

    public function singleContact($id);

    public function sendMessage(array $data);

    public function getAllAdminListGroup($search);

    public function getStatus();

    public function find($id);

    public function closeStatus();

    public function openStatus();

    public function save(ChatGroup $chatGroup);

    public function delete(ChatGroup $chatGroup);

    public function getContacts();

    public function findContact($id);

    public function seen(ChatGroup $group);

    public static function isOpen(ChatGroup $group);
}
