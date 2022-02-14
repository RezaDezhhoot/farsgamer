<?php

namespace App\Http\Livewire\Admin\Tickets;

use App\Models\Notification;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\User;
use App\Sends\SendMessages;
use App\Traits\Admin\ChatList;
use App\Traits\Admin\Sends;
use App\Traits\Admin\TextBuilder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StoreTicket extends Component
{
    use AuthorizesRequests , Sends , TextBuilder ,ChatList;
    public $ticket , $header , $mode , $data = [];
    public $subject , $user_id , $content , $file , $priority , $status , $child = [] , $user_name , $answer , $answerFile;
    public function mount($action , $id = null)
    {
        $this->authorize('show_tickets');

        if ($action == 'edit') {
            $this->ticket = Ticket::findOrFail($id);
            $this->header = " تیکت شماره $id فرستنده : ".Ticket::getSenderType()[$this->ticket->sender_type]." ";
            $this->subject = $this->ticket->subject;
            $this->user_id = $this->ticket->user_id;
            $this->user_name = $this->ticket->user->user_name;
            $this->content = $this->ticket->content;
            $this->file = $this->ticket->file;
            $this->priority = $this->ticket->priority;
            $this->status = $this->ticket->status;
            $this->child = $this->ticket->child;
            $this->chatUserId = $this->ticket->user->id;
            $this->chats = \auth()->user()->singleContact($this->ticket->user->id);
        } else $this->header = 'تیکت جدید';
        $this->mode = $action;
        $this->data['priority'] = Ticket::getPriority();
        $this->data['status'] = Ticket::getStatus();
        $this->data['user'] = User::all()->pluck('user_name','id');
        $this->data['subject'] = Setting::getSingleRow('subject',[]);

    }


    public function store()
    {
        $this->authorize('edit_tickets');
        if ($this->mode == 'edit')
            $this->saveInDataBase($this->ticket);
        elseif ($this->mode == 'create') {
            $this->saveInDataBase(new Ticket());
            $this->reset(['subject','user_id','content','file','priority','status']);
        }
    }

    public function saveInDataBase(Ticket $model)
    {
        $this->validate(
            [
                'subject' => ['required','string','max:250'],
                'user_id' => ['required','exists:users,id'],
                'content' => ['required','string','max:95000'],
                'file' => ['nullable','string','max:800'],
                'priority' => ['required','in:'.implode(',',array_keys(Ticket::getPriority()))],
                'status' => ['required', 'in:'.implode(',',array_keys(Ticket::getStatus()))],
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
        $model->sender_id  = \auth()->id();
        $model->sender_type  = Ticket::ADMIN;
        $model->priority = $this->priority;
        $model->status = $this->status;
        $model->save();
        if ($this->mode == 'create') {
            $text = $this->createText('new_ticket',$model);
            $send = new SendMessages();
            $send->sends($text,$model->user,Notification::TICKET,$model->id);
        }
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function deleteItem()
    {
        $this->ticket->delete();
        return redirect()->route('admin.ticket');
    }


    public function newAnswer()
    {
        $this->authorize('edit_tickets');
        $this->validate(
            [
                'answer' => ['required', 'string'],
                'answerFile' => ['nullable' , 'max:250','string']
            ] , [] , [
                'answer' => 'پاسخ',
                'answerFile' => 'فایل'
            ]
        );
        $new = new Ticket();
        $new->subject = $this->subject;
        $new->user_id  = $this->user_id;
        $new->parent_id = $this->ticket->id;
        $new->content = $this->answer;
        $new->file = $this->answerFile;
        $new->sender_id = Auth::id();
        $new->sender_type = Ticket::ADMIN;
        $new->priority = $this->priority;
        $new->status = Ticket::ANSWERED;
        $text = $this->createText('ticket_answer',$new);
        $send = new SendMessages();
        $send->sends($text,$this->ticket->user,Notification::TICKET,$this->ticket->id);
        $new->save();
        $this->child->push($new);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }


    public function delete($key)
    {
        $this->authorize('delete_tickets');
        $ticket = $this->child[$key];
        $ticket->delete();
        unset($this->child[$key]);
    }



    public function render()
    {
        return view('livewire.admin.tickets.store-ticket')->extends('livewire.admin.layouts.admin');
    }
}
