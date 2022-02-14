<?php

namespace App\Http\Livewire\Admin\Tickets;

use App\Http\Livewire\BaseComponent;
use App\Models\Setting;
use App\Models\Ticket;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;

class IndexTicket extends BaseComponent
{
    use WithPagination , AuthorizesRequests;
    public $pagination = 10 , $search , $status , $priority , $subject , $data = [] , $placeholder = 'شماره یا نام کاربری کاربر';

    protected $queryString = ['status','priority','subject'];

    public function render()
    {
        $this->authorize('show_tickets');

        $tickets = Ticket::latest('id')->with(['user'])->
        where('parent_id',null)->when($this->status, function ($query) {
            return $query->where('status' , $this->status);
        })->when($this->priority, function ($query) {
            return $query->where('priority' , $this->priority);
        })->when($this->subject, function ($query) {
            return $query->where('subject' , $this->subject);
        })->when($this->search,function ($query){
            return $query->whereHas('user',function ($query){
                return is_numeric($this->search) ?
                    $query->where('phone',$this->search) : $query->where('user_name',$this->search);
            });
        })->paginate($this->pagination);

        $this->data['status'] = Ticket::getStatus();
        $this->data['priority'] = Ticket::getPriority();
        $this->data['subject'] = Setting::getSingleRow('subject',[]);

        return view('livewire.admin.tickets.index-ticket',['tickets' => $tickets])->extends('livewire.admin.layouts.admin');
    }

    public function delete($id)
    {
        $this->authorize('delete_tickets');
        Ticket::findOrFail($id)->delete();
    }
}
