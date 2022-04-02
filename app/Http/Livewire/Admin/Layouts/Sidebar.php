<?php

namespace App\Http\Livewire\Admin\Layouts;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\CardRepositoryInterface;
use App\Repositories\Interfaces\CommentRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\RequestRepositoryInterface;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;

class Sidebar extends BaseComponent
{
    public function render
    (
        OrderRepositoryInterface $orderRepository , TicketRepositoryInterface $ticketRepository , CommentRepositoryInterface $commentRepository,
        UserRepositoryInterface $userRepository , CardRepositoryInterface $cardRepository , RequestRepositoryInterface $requestRepository
    )
    {
        $data = [
            'orders' => $orderRepository::getNew(),
            'tickets' => $ticketRepository::getNew(),
            'comments' => $commentRepository::getNew(),
            'users' => $userRepository::getNew(),
            'cards' => $cardRepository::getNew(),
            'requests' => $requestRepository::getNew(),
        ];
        return view('livewire.admin.layouts.sidebar',$data);
    }
}
