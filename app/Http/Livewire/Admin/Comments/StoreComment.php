<?php

namespace App\Http\Livewire\Admin\Comments;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\CommentRepositoryInterface;
use App\Traits\Admin\ChatList;

class StoreComment extends BaseComponent
{
    use ChatList;
    public $comment , $data = [] , $mode , $header , $content , $score , $status , $type , $case;
    public function mount(CommentRepositoryInterface $commentRepository,$action , $id = null)
    {
        $this->authorizing('show_comments');
        if ($action == 'edit')
        {
            $this->comment = $commentRepository->find($id,false);
            $this->header = $this->comment->user->user_name;
            $this->status = $this->comment->status;
            $this->content = $this->comment->content;
            $this->type = $this->comment->commentableTypeLabel;
            $this->case = $this->comment->commentable->title ?? $this->comment->commentable->user_name;
            $this->score = $this->comment->score;
            $this->chatUserId = $this->comment->user->id;
            $this->chats = auth()->user()->singleContact($this->comment->user->id);
        } else abort(404);

        $this->data['type'] = $commentRepository->getFor();
        $this->data['status'] = $commentRepository->getStatus();
        $this->mode = $action;
    }

    public function store(CommentRepositoryInterface $commentRepository)
    {
        $this->authorizing('edit_comments');
        $this->validate([
            'status' => ['required','in:'.implode(',',array_keys($commentRepository->getStatus()))],
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
        $commentRepository->save($this->comment);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

    public function deleteItem(CommentRepositoryInterface $commentRepository)
    {
        $this->authorizing('delete_comments');
        $commentRepository->delete($this->comment);
        return redirect()->route('admin.comment');
    }

    public function render()
    {
        return view('livewire.admin.comments.store-comment')
            ->extends('livewire.admin.layouts.admin');
    }
}
