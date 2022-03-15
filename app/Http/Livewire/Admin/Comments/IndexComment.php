<?php

namespace App\Http\Livewire\Admin\Comments;

use App\Http\Livewire\BaseComponent;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Comment;
use Livewire\WithPagination;

class IndexComment extends BaseComponent
{
    use AuthorizesRequests , WithPagination;
    protected $queryString = ['for','status'];
    public $data = [] , $status ,$for , $search , $pagination = 10 , $placeholder = 'نام کاربری یا شماره کاربر';
    public function render()
    {
        $this->authorize('show_comments');
        $comments = Comment::latest('id')->with(['user'])->when($this->search,function ($query){
            return $query->whereHas('user',function ($query){
                return is_numeric($this->search) ?
                    $query->where('phone',$this->search) : $query->where('user_name',$this->search);
            });
        })->when($this->status,function ($query){
            return $query->where('status',$this->status);
        })->when($this->for,function ($query){
            return $query->where('commentable_type',$this->for);
        })->paginate($this->pagination);

        $this->data['status'][Comment::CONFIRMED] = Comment::getStatus()[Comment::CONFIRMED].' ('.Comment::where('status',Comment::CONFIRMED)->count().')';
        $this->data['status'][Comment::UNCONFIRMED] = Comment::getStatus()[Comment::UNCONFIRMED].' ('.Comment::where('status',Comment::UNCONFIRMED)->count().')';
        $this->data['status'][Comment::NEW] = Comment::getStatus()[Comment::NEW].' ('.Comment::where('status',Comment::NEW)->count().')';
        $this->data['for'] = Comment::getFor();
        return view('livewire.admin.comments.index-comment',['comments'=>$comments])->extends('livewire.admin.layouts.admin');
    }

    public function delete($id)
    {
        $this->authorize('delete_comments');
        Comment::findOrFail($id)->delete();
    }

    public function confirm($id)
    {
        $this->authorize('edit_comments');
        $comment = Comment::findOrFail($id);
        $comment->status = Comment::CONFIRMED;
        $comment->save();
        $this->emitNotify('اطلاعات با موفقیت ثبت شد');
    }

}
