<?php

namespace App\Http\Livewire\Site\Dashboard\Tickets;

use App\Http\Livewire\BaseComponent;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\User;
use App\Traits\Admin\Sends;
use App\Traits\Admin\TextBuilder;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\WithFileUploads;

class StoreTicket extends BaseComponent
{
    use WithFileUploads , Sends , TextBuilder;
    public $ticket , $user , $header , $mode , $disabled = false , $data = [];
    public $subject , $content , $file , $priority , $status , $newMessage  , $final_message;

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

        $rateKey = 'verify-attempt:' . Auth::user()->user_name . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $this->reset(['subject','content','file']);
            return $this->addError('error', 'زیادی تلاش کردید. لطفا پس از مدتی دوباره تلاش کنید.');
        }

        RateLimiter::hit($rateKey, 3 * 60 * 60);
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
        if ($this->mode == 'create')
        {
            $this->validate([
                'subject' => ['required','string','in:'.implode(',',$this->data['subject'])],
                'content' => ['required','string','max:250'],
                'file' => ['nullable','image','mimes:jpg,jpeg,png','max:2048'],
                'priority' => ['required','in:'.Ticket::HIGH.','.Ticket::NORMAL.','.Ticket::HIGH],
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
            $model->status  = Ticket::OPEN;
            if ($this->file <> null)
                $model->file  = 'storage/'.$this->file->store('files/ticket', 'public');
            $model->save();
            $this->emitNotify('اطلاعات با موفقیت ثبت شد');
            redirect()->route('user.store.ticket',['edit',$model->id]);

        } elseif ($this->mode == 'edit') {
            if ($model->status == Ticket::OPEN) {
                $this->validate([
                    'newMessage' => ['required','string','max:250'],
                ],[],[
                    'newMessage' => 'متن درخواست',
                ]);
                $ticket = new Ticket();
                $ticket->subject = $model->subject;
                $ticket->user_id  = Auth::id();
                $ticket->content = $this->newMessage;
                $ticket->parent_id = $model->id;
                $ticket->sender_id = Auth::id();
                $ticket->priority = $model->priority;
                $ticket->status = $model->status;
                $ticket->save();
                $this->emitNotify('اطلاعات با موفقیت ثبت شد');
                $this->ticket->child->push($ticket);
                $this->reset(['newMessage']);
            } else {
                $this->addError('error','این درخواست بسته شده است');
                return;
            }

        }
    }

    public function uploadFile()
    {
        // upon form submit, this function till fill your progress bar
    }


    public function render()
    {
        return view('livewire.site.dashboard.tickets.store-ticket');
    }
}
