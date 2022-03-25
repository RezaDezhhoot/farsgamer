<?php

namespace App\Repositories\Interfaces;


use App\Models\Ticket;

interface TicketRepositoryInterface
{
    public function getAllAdminList($search , $status , $priority , $subject , $pagination);

    public function save(Ticket $ticket);

    public function delete(Ticket $ticket);

    public function find($id);

    public static function getStatus();

    public static function getPriority();

    public static function admin();

    public static function user();

    public static function answerStatus();

    public static function pendingStatus();

    public static function userAnsweredStatus();

    public static function highPriority();

    public static function normalPriority();

    public static function lowPriority();

    public static function getSenderType();

    public function newTicketObject();

    public static function getNew();
}
