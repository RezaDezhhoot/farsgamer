<?php

namespace App\Http\Livewire\Site\Dashboard\Tickets;

use App\Http\Livewire\BaseComponent;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\User;
use App\Traits\Admin\TextBuilder;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\WithFileUploads;
use App\Sends\SendMessages;

class StoreTicket extends BaseComponent
{
    use WithFileUploads , TextBuilder;
    public $ticket , $user , $header , $mode , $disabled = false , $data = [] , $i;
    public $subject , $content , $file = [] , $priority , $status , $newMessage  , $final_message , $newFile;
    public function mount($action,$id = null)
    {
        SEOMeta::setTitle('پشتیبانی',false);
        SEOMeta::setDescription(Setting::getSingleRow('seoDescription'));
        SEOMeta::addKeyword(explode(',',Setting::getSingleRow('seoKeyword')));
        OpenGraph::setUrl(url()->current());
        OpenGraph::setTitle('پشتیبانی');
        OpenGraph::setDescription(Setting::getSingleRow('seoDescription'));
        TwitterCard::setTitle('پشتیبانی');
        TwitterCard::setDescription(Setting::getSingleRow('seoDescription'));
        JsonLd::setTitle('پشتیبانی');
        JsonLd::setDescription(Setting::getSingleRow('seoDescription'));
        JsonLd::addImage(Setting::getSingleRow('logo'));
        if ($action == 'edit') {
            $this->user = User::findOrFail(Auth::id());
            $this->ticket = $this->user->tickets()->with(['child'])->findOrFail($id);
            $this->header = $this->ticket->subject;
            $this->subject = $this->ticket->subject;
            $this->content = $this->ticket->content;
            $this->file = $this->ticket->file;
            $this->priority = $this->ticket->priority;
            $this->disabled = true;
        } elseif ($action == 'create') {
            $this->header = 'درخواست جدید ';
            $this->subject = 'انتخاب';
            $this->priority = 'انتخاب';
        } else abort(404);

        $this->mode = $action;
        $this->data['subject'] = Setting::getSingleRow('subject',[]);
        $this->data['priority'] = Ticket::getPriority();
    }

    public function store()
    {
        $rateKey = 'ticket:' . Auth::user()->user_name . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey, Setting::getSingleRow('ticket_per_day'))) {
            $this->reset(['subject','content','file']);
            return $this->addError('error', 'زیادی تلاش کردید. لطفا پس از مدتی دوباره تلاش کنید.');
        }

        RateLimiter::hit($rateKey, 24 * 60 * 60);
        if ($this->mode == 'edit')
            $this->saveInDataBase($this->ticket);
        elseif ($this->mode == 'create')
        {
            $this->saveInDataBase(new Ticket());
            $this->reset(['subject','content','file','priority','final_message','newMessage']);

        } else abort(404);
    }

    public function saveInDataBase(Ticket $model)
    {
        $this->resetErrorBag();
        if ($this->mode == 'create')
        {
            $this->validate([
                'subject' => ['required','string','in:'.implode(',',$this->data['subject'])],
                'content' => ['required','string','max:18500'],
                'file' => ['array','min:0','max:4'],
                'file.*' => ['nullable','mimes:'.Setting::getSingleRow('valid_ticket_files'),'max:2048'],
                'priority' => ['in:'.Ticket::HIGH.','.Ticket::NORMAL.','.Ticket::HIGH],
            ],[],[
                'subject' => 'موضوع',
                'content' => 'متن درخواست',
                'file' => 'فایل',
                'priority' => 'الویت',
            ]);
            $this->uploadFile();
            $model->subject = $this->subject;
            $model->user_id  = Auth::id();
            $model->content = $this->content;
            $model->sender_id  = Auth::id();
            $model->priority  = $this->priority;
            $model->status = Ticket::PENDING;

            if (!is_null($this->file) && !empty($this->file)){
                $files = [];
                foreach ($this->file as $item){
                    $save = 'storage/'.$item->store('ticket', 'public');
                    array_push($files,$save);
                }
                $model->file = implode(',',$files);
            }
            $model->save();
            $this->emitNotify('اطلاعات با موفقیت ثبت شد');
            redirect()->route('user.store.ticket',['edit',$model->id]);

        } elseif ($this->mode == 'edit') {
            if ($model->status == Ticket::ANSWERED) {
                $this->validate([
                    'newMessage' => ['required','string','max:250'],
                    'file' => ['array','min:0','max:4'],
                    'file.*' => ['nullable','mimes:'.Setting::getSingleRow('valid_ticket_files'),'max:2048'],
                ],[],[
                    'newMessage' => 'متن درخواست',
                    'file' =>  'فایل',
                ]);
                $ticket = new Ticket();
                $ticket->subject = $model->subject;
                $ticket->user_id  = Auth::id();
                $ticket->content = $this->newMessage;
                $ticket->parent_id = $model->id;
                $ticket->sender_id = Auth::id();
                $ticket->priority = $model->priority;
                $ticket->status = $model->status;
                if (!is_null($this->file) && !empty($this->file)){
                    $files = [];
                    foreach ($this->file as $item){
                        $save = 'storage/'.$item->store('ticket', 'public');
                        array_push($files,$save);
                    }
                    $ticket->file = implode(',',$files);
                }
                $ticket->save();
                $this->emitNotify('اطلاعات با موفقیت ثبت شد');
                $this->ticket->child->push($ticket);
                $model->status  = Ticket::USER_ANSWERED;
                $model->save();
                $this->reset(['newMessage','newFile']);
            } else {
                $this->addError('error','لطفا تا ارسال پاسخ توسط مدیریت منتظر بمانید');
                return;
            }

        }
    }

    public function addFileInput()
    {
        $this->i = $this->i + 1;
        array_push($this->file,$this->i);
    }

    public function deleteImage($key)
    {
        unset($this->file[$key]);
    }

    public function uploadFile()
    {
        // upon form submit, this function till fill your progress bar
    }

    public function render()
    {
        return view('livewire.site.dashboard.tickets.store-ticket')
            ->extends('livewire.site.layouts.site.site');
    }
}
