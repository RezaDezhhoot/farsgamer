<?php


namespace App\Repositories\Classes;

use App\Models\Chat;
use App\Models\ChatGroup;
use App\Repositories\Interfaces\ChatRepositoryInterface;


class ChatRepository implements ChatRepositoryInterface
{
    public function startChat($id)
    {
        $contact = $this->singleContact($id);
        if ($contact === null){
            $contact = ChatGroup::create([
                'slug' => uniqid(),
                'user1' => auth('api')->id(),
                'user2' => $id,
                'status' => ChatGroup::OPEN,
            ]);
        }
        return $contact;
    }

    public function contacts()
    {
        return ChatGroup::where(function ($query){
            return $query->where('user1',auth('api')->id())->orWhere('user2',auth('api')->id());
        });
    }

    public function singleContact($id)
    {
        return $this->contacts()->where(function ($query) use ($id) {
            if ($id == auth('api')->id())
                return $query->whereColumn('user1', 'user2');
            else
                return $query->where('user1',$id)->orWhere('user2',$id)->first();
        })->first();
    }

    public function sendMessage(array $data)
    {
        return Chat::create($data);
    }

    /**
     * @param $search
     * @return mixed
     */
    public function getAllAdminListGroup($search)
    {
        return ChatGroup::latest('id')->with(['user_one','user_two','chats'])
            ->when($search,function ($query) use ($search){
                return $query->whereHas('user_one',function ($query) use ($search){
                    return is_numeric($search)
                        ? $query->where('phone',$search) : $query->where('user_name',$search);
                })->orWhereHas('user_two',function ($query) use ($search){
                    return is_numeric($search)
                        ? $query->where('phone',$search) : $query->where('user_name',$search);
                })->orWhere('slug',$search);
            })->whereColumn('user1', '!=' ,'user2')->get();
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return ChatGroup::getStatus();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return ChatGroup::findOrFail($id);
    }

    /**
     * @return mixed
     */
    public function closeStatus()
    {
        return ChatGroup::CLOSE;
    }

    /**
     * @return mixed
     */
    public function openStatus()
    {
        return ChatGroup::OPEN;
    }

    /**
     * @param ChatGroup $chatGroup
     * @return mixed
     */
    public function save(ChatGroup $chatGroup)
    {
        $chatGroup->save();
        return $chatGroup;
    }

    /**
     * @param ChatGroup $chatGroup
     * @return mixed
     */
    public function delete(ChatGroup $chatGroup)
    {
        return $chatGroup->delete();
    }
}
