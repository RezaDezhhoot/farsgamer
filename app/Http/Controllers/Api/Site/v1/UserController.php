<?php

namespace App\Http\Controllers\Api\Site\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\CommentCollection;
use App\Http\Resources\v1\OrderCollection;
use App\Http\Resources\v1\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{

    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke($user)
    {
        $user_object = $this->userRepository->getUser('user_name',$user);
        return response([
            'data' => [
                'user' => new User($user_object),
                'orders' => new OrderCollection($this->userRepository->getMyOrders($user_object,false)),
                'comment' => new CommentCollection($this->userRepository->getMyComments($user_object))
            ],
            'status' => 'success',
        ],Response::HTTP_OK);
    }
}
