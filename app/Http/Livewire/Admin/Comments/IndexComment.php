<?php

namespace App\Http\Livewire\Admin\Comments;

use App\Http\Livewire\BaseComponent;
use App\Repositories\Interfaces\CommentRepositoryInterface;
use Livewire\WithPagination;

class IndexComment extends BaseComponent
{
    use WithPagination;
    protected $queryString = ['for','status'];
    public $data = [] , $status , $for , $placeholder = 'نام کاربری یا شماره کاربر';
    public function render(CommentRepositoryInterface $commentRepository)
    {
        $this->authorizing('show_comments');
        $comments = $commentRepository->getAllAdminList($this->search,$this->status,$this->for,$this->pagination,false);
        foreach ($commentRepository->getStatus() as $key => $value)
            $this->data['status'][$key] = $value.' ('.$commentRepository->getByConditionCount('status','=',$key).')';

        $this->data['for'] = $commentRepository->getFor();
        return view('livewire.admin.comments.index-comment',['comments'=>$comments])
            ->extends('livewire.admin.layouts.admin');
    }

    public function delete($id , CommentRepositoryInterface $commentRepository)
    {
        $this->authorizing('delete_comments');
        $comment = $commentRepository->find($id,false);
        $commentRepository->delete($comment);
    }

    public function confirm($id , CommentRepositoryInterface $commentRepository)
    {
        $this->authorizing('edit_comments');
        $comment = $commentRepository->find($id,false);
        $comment->status = $commentRepository::confirmedStatus();
        $commentRepository->save($comment);
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

}
