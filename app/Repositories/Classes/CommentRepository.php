<?php


namespace App\Repositories\Classes;

use App\Models\Comment;
use App\Repositories\Interfaces\CommentRepositoryInterface;

class CommentRepository implements CommentRepositoryInterface
{
    /**
     * @param $search
     * @param $status
     * @param $for
     * @param $pagination
     * @param bool $active
     * @return mixed
     */
    public function getAllAdminList($search, $status, $for, $pagination, $active = true)
    {
        return Comment::active($active)->latest('id')->with(['user'])->when($search,function ($query) use ($search){
            return $query->whereHas('user',function ($query) use ($search){
                return is_numeric($search) ?
                    $query->where('phone',$search) : $query->where('user_name',$search);
            });
        })->when($status,function ($query) use ($status){
            return $query->where('status',$status);
        })->when($for,function ($query) use ($for){
            return $query->where('commentable_type',$for);
        })->paginate($pagination);
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return Comment::getStatus();
    }

    /**
     * @param $col
     * @param $operator
     * @param $value
     * @param bool $active
     * @return mixed
     */
    public function getByConditionCount($col, $operator, $value, $active = true)
    {
        return Comment::active($active)->where("$col","$operator","$value")->count();
    }

    /**
     * @param $id
     * @param bool $active
     * @return mixed
     */
    public function find($id , $active = true)
    {
        return Comment::active($active)->findOrFail($id);
    }

    /**
     * @param Comment $comment
     * @return mixed
     */
    public function delete(Comment $comment)
    {
        return $comment->delete();
    }

    /**
     * @param Comment $comment
     * @return mixed
     */
    public function save(Comment $comment)
    {
        $comment->save();
        return $comment;
    }

    /**
     * @return mixed
     */
    public function getFor()
    {
        return Comment::getFor();
    }

    /**
     * @return mixed
     */
    public static function confirmedStatus()
    {
        return Comment::CONFIRMED;
    }

    /**
     * @return mixed
     */
    public static function unconfirmedStatus()
    {
        return Comment::UNCONFIRMED;
    }

    /**
     * @return mixed
     */
    public static function newStatus()
    {
        return Comment::NEW;
    }
}
