<?php

namespace App\Http\Livewire\Admin\Comments;

use App\Http\Livewire\BaseComponent;
use App\Models\Comment;
use App\Traits\Admin\ChatList;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StoreComment extends BaseComponent
{
    use AuthorizesRequests , ChatList;
    public $comment , $data = [] , $mode , $header , $content , $score , $status , $type , $case;
    public function mount($action , $id=null)
    {
        $this->authorize('show_comments');
        if ($action == 'edit')
        {
            $this->comment = Comment::findOrFail($id);
            $this->header = $this->comment->user->user_name;
            $this->status = $this->comment->status;
            $this->content = $this->comment->content;
            $this->type = $this->comment->type;
            $this->case = $this->comment->target;
            $this->score = $this->comment->score;
            $this->chatUserId = $this->comment->user->id;
            $this->chats = \auth()->user()->singleContact($this->comment->user->id);
        } else abort(404);

        $this->data['type'] = Comment::getFor();
        $this->data['status'] = Comment::getStatus();
        $this->mode = $action;
    }

    public function store()
    {
        $this->authorize('edit_comments');
        $this->validate([
            'status' => ['required','in:'.Comment::CONFIRMED.','.Comment::UNCONFIRMED],
            'content' => ['required','string','max:250'],
            'score' => ['required','numeric','between:0,5'],
        ],[],[
            'status' => 'وضعیت',
            'content' => 'متن',
            'score' => 'امتیاز',
        ]);

        $this->comment->status = $this->status;
        $this->comment->content = $this->content;
        $this->comment->score = $this->score;
        $this->comment->save();
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function deleteItem()
    {
        $this->authorize('delete_comments');
        $this->comment->delete();
        return redirect()->route('admin.comment');
    }

    public function render()
    {
        return view('livewire.admin.comments.store-comment')->extends('livewire.admin.layouts.admin');
    }
}
