<?php

namespace App\Http\Controllers\Api\Site\v1\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\NotificationCollection;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private $userRepository;
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke()
    {
        $user = $this->userRepository->find(auth()->id());
        return response([
            'data' => [
                'details' => [
                    'orders' => $user->orders()->count(),
                    'orders_views_count' => $user->orders->sum('view_count'),
                    'orders_has_transaction' => $user->orders_has_transaction,
                ],
                'notification' => [
                    'records' => new NotificationCollection($this->userRepository->getLastNotifications($user,'all')),
                ],
            ]
        ]);
        // TODO: Implement __invoke() method.
    }
}
