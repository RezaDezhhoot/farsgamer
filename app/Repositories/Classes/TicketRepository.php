<?php


namespace App\Repositories\Classes;

use App\Models\Ticket;
use App\Models\User;
use App\Repositories\Interfaces\TicketRepositoryInterface;


class TicketRepository implements TicketRepositoryInterface
{

    public function getAllAdminList($search, $status, $priority, $subject, $pagination)
    {
        return Ticket::latest('id')->with(['user'])->
        where('parent_id',null)->when($status, function ($query) use ($status) {
            return $query->where('status' , $status);
        })->when($priority, function ($query) use ($priority) {
            return $query->where('priority' , $priority);
        })->when($subject, function ($query) use ($subject) {
            return $query->where('subject' , $subject);
        })->when($search,function ($query) use ($search){
            return $query->whereHas('user',function ($query) use ($search){
                return is_numeric($search) ?
                    $query->where('phone',$search) : $query->where('user_name',$search);
            });
        })->paginate($pagination);
    }

    public function save(Ticket $ticket)
    {
        $ticket->save();
        return $this;
    }

    public function delete(Ticket $ticket)
    {
        return $ticket->delete();
    }

    public function find($id)
    {
        return Ticket::findOrFail($id);
    }

    public static function getStatus()
    {
        return Ticket::getStatus();
    }

    public static function getPriority()
    {
        return Ticket::getPriority();
    }

    public static function admin()
    {
        return Ticket::ADMIN;
    }

    public static function user()
    {
        return Ticket::USER;
    }

    public static function answerStatus()
    {
        return Ticket::ANSWERED;
    }

    public static function pendingStatus()
    {
        return Ticket::PENDING;
    }

    public static function userAnsweredStatus()
    {
        return Ticket::USER_ANSWERED;
    }

    public static function highPriority()
    {
        return Ticket::HIGH;
    }

    public static function normalPriority()
    {
        return Ticket::NORMAL;
    }

    public static function lowPriority()
    {
        return Ticket::LOW;
    }

    public static function getSenderType()
    {
        return Ticket::getSenderType();
    }

    public function newTicketObject()
    {
        return new Ticket();
    }

    public static function getNew()
    {
        // TODO: Implement getNew() method.
        return Ticket::getNew();
    }

    public function getUserTickets(User $user)
    {
        return Ticket::latest('id')->whereNull('parent_id')->paginate(25);
        // TODO: Implement getUserTickets() method.
    }

    public function create(User $user, array $data)
    {
        return $user->tickets()->create($data);
        // TODO: Implement create() method.
    }

    public function userTicketFind(User $user, $id)
    {
        return $user->tickets()->findOrFail($id);
        // TODO: Implement userTicketFind() method.
    }
}
