<?php

namespace App\Http\Livewire\Admin\Tickets;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\ChatRepositoryInterface;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Traits\Admin\ChatList;

class StoreTicket extends BaseComponent
{
    use ChatList;
    public $ticket , $header , $mode , $data = [];
    public $subject , $user_id , $content , $file , $priority , $status , $child = [] , $user_name , $answer , $answerFile;
    public function mount(
        TicketRepositoryInterface $ticketRepository, SettingRepositoryInterface $settingRepository,
        ChatRepositoryInterface $chatRepository,UserRepositoryInterface $userRepository,$action , $id = null
    )
    {
        $this->authorizing('show_tickets');

        if ($action == 'edit') {
            $this->ticket = $ticketRepository->find($id);
            $this->header = " تیکت شماره $id فرستنده : ".$ticketRepository::getSenderType()[$this->ticket->sender_type]." ";
            $this->subject = $this->ticket->subject;
            $this->user_id = $this->ticket->user_id;
            $this->user_name = $this->ticket->user->user_name;
            $this->content = $this->ticket->content;
            $this->file = $this->ticket->file;
            $this->priority = $this->ticket->priority;
            $this->status = $this->ticket->status;
            $this->child = $this->ticket->child;
            $this->chatUserId = $this->ticket->user->id;
            $this->chats = $chatRepository->singleContact($this->ticket->user->id);
        } else $this->header = 'تیکت جدید';
        $this->mode = $action;
        $this->data['priority'] = $ticketRepository::getPriority();
        $this->data['status'] = $ticketRepository::getStatus();
        $this->data['user'] = $userRepository->pluck('user_name','id','user_name');
        $this->data['subject'] = $settingRepository->getSiteFaq('subject',[]);
    }


    public function store(TicketRepositoryInterface $ticketRepository)
    {
        $this->authorizing('edit_tickets');
        if ($this->mode == 'edit')
            $this->saveInDataBase($ticketRepository,$this->ticket);
        elseif ($this->mode == 'create') {
            $this->saveInDataBase($ticketRepository,$ticketRepository->newTicketObject());
            $this->reset(['subject','user_id','content','file','priority','status']);
        }
    }

    public function saveInDataBase($ticketRepository, $model)
    {
        $this->validate(
            [
                'subject' => ['required','string','max:250'],
                'user_id' => ['required','exists:users,id'],
                'content' => ['required','string','max:95000'],
                'file' => ['nullable','string','max:800'],
                'priority' => ['required','in:'.implode(',',array_keys($ticketRepository::getPriority()))],
                'status' => ['required', 'in:'.implode(',',array_keys($ticketRepository::getStatus()))],
            ] , [] , [
                'subject' => 'موضوع',
                'user_id' => 'کاربر',
                'content' => 'متن',
                'file' => 'فایل',
                'priority' => 'الویت',
                'status' => 'وضعیت',
            ]
        );
        $model->subject = $this->subject;
        $model->user_id = $this->user_id;
        $model->content = $this->content;
        $model->file = $this->file;
        $model->parent_id = null;
        $model->sender_id  = auth()->id();
        $model->sender_type  = $ticketRepository::admin();
        $model->priority = $this->priority;
        $model->status = $this->status;
        $ticketRepository->save($model);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function deleteItem(TicketRepositoryInterface $ticketRepository)
    {
        $ticketRepository->delete($this->ticket);
        return redirect()->route('admin.ticket');
    }


    public function newAnswer(TicketRepositoryInterface $ticketRepository)
    {
        $this->authorizing('edit_tickets');
        $this->validate(
            [
                'answer' => ['required', 'string','max:6500'],
                'answerFile' => ['nullable' , 'max:250','string']
            ] , [] , [
                'answer' => 'پاسخ',
                'answerFile' => 'فایل'
            ]
        );
        $new = $ticketRepository->newTicketObject();
        $new->subject = $this->subject;
        $new->user_id  = $this->user_id;
        $new->parent_id = $this->ticket->id;
        $new->content = $this->answer;
        $new->file = $this->answerFile;
        $new->sender_id = auth()->id();
        $new->sender_type = $ticketRepository::admin();
        $new->priority = $this->priority;
        $new->status = $ticketRepository::answerStatus();
        $this->ticket->status = $ticketRepository::answerStatus();
        $ticketRepository->save($this->ticket);
        $ticketRepository->save($new);
        $this->child->push($new);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }


    public function delete(TicketRepositoryInterface $ticketRepository,$key)
    {
        $this->authorizing('delete_tickets');
        $ticket = $this->child[$key];
        $ticketRepository->delete($ticket);
        unset($this->child[$key]);
    }

    public function render()
    {
        return view('livewire.admin.tickets.store-ticket')->extends('livewire.admin.layouts.admin');
    }
}
