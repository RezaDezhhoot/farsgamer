<?php
namespace App\Repositories\Interfaces;
use Illuminate\Http\Request;

interface NotificationRepositoryInterface
{
    public function cardStatus();

    public function requestStatus();

    public function getSubjects();

    public function create(array $data);

    public function privateType();

    public function publicType();
}
