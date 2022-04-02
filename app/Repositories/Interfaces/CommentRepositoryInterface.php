<?php

namespace App\Repositories\Interfaces;

use App\Models\Comment;

interface CommentRepositoryInterface
{
    public function getAllAdminList($search , $status , $for , $pagination , $active = true);

    public function getStatus();

    public function getByConditionCount($col , $operator , $value , $active = true);

    public function find($id , $active = true);

    public function delete(Comment $comment);

    public function save(Comment $comment);

    public function getFor();

    public static function confirmedStatus();

    public static function unconfirmedStatus();

    public static function newStatus();

    public static function getNew();
}
