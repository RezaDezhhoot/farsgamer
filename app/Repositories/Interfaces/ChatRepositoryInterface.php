<?php

namespace App\Repositories\Interfaces;

interface ChatRepositoryInterface
{
    public function startChat($id);

    public function contacts();

    public function singleContact($id);

    public function sendMessage($chat);
}
