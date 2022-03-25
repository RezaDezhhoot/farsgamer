<?php

namespace App\Http\Livewire\Admin\Tickets;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use Livewire\WithPagination;

class IndexTicket extends BaseComponent
{
    use WithPagination;
    public $status , $priority , $subject , $data = [] , $placeholder = 'شماره یا نام کاربری کاربر';

    protected $queryString = ['status','priority','subject'];

    public function render(TicketRepositoryInterface $ticketRepository , SettingRepositoryInterface $settingRepository)
    {
        $this->authorizing('show_tickets');

        $tickets = $ticketRepository->getAllAdminList($this->search,$this->status,$this->priority,$this->subject,$this->pagination);

        $this->data['status'] = $ticketRepository::getStatus();
        $this->data['priority'] = $ticketRepository::getPriority();
        $this->data['subject'] = $settingRepository->getSiteFaq('subject',[]);

        return view('livewire.admin.tickets.index-ticket',['tickets' => $tickets])->extends('livewire.admin.layouts.admin');
    }

    public function delete(TicketRepositoryInterface $ticketRepository ,$id)
    {
        $this->authorizing('delete_tickets');
        $ticket = $ticketRepository->find($id);
        $ticketRepository->delete($ticket);
    }
}
