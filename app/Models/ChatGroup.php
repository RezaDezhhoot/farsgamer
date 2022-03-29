<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static latest(string $string)
 * @method static findOrFail($id)
 * @method static where(\Closure $param)
 * @method static create(array $array)
 * @property mixed|string slug
 * @property int|mixed|string|null user1
 * @property mixed user2
 * @property mixed|string status
 * @property int|mixed is_admin
 */
class ChatGroup extends Model
{
    use HasFactory;
    protected $table = 'chats_groups';
    const OPEN = 'open' , CLOSE = 'close';

    protected $guarded = [];

    public function user_one()
    {
        return $this->belongsTo(User::class,'user1');
    }

    public function user_two()
    {
        return $this->belongsTo(User::class,'user2');
    }

    public function getStatusLabelAttribute()
    {
        return self::getStatus()[$this->status];
    }

    public function chats()
    {
        return $this->hasMany(Chat::class,'group_id')->with(['sender','receiver']);
    }

    public function getLastAttribute()
    {
        $last = $this->chats()->orderByDesc('id')->first()->created_at ?? null;
        return (!empty($last) && !is_null($last)) ? $last->diffForHumans() : '';
    }

    public function getLastTextAttribute()
    {
        $last = $this->chats()->orderByDesc('id')->first()->content ?? null;
        return (!empty($last) && !is_null($last)) ? $last : '';
    }

    public function getLastSenderAttribute()
    {
        $last = $this->chats()->orderByDesc('id')->first()->sender ?? null;
        return (!empty($last) && !is_null($last)) ? $last->id : '';
    }

    public static function getStatus(){
        return [
            self::OPEN => 'باز',
            self::CLOSE => 'بسته',
        ];
    }

    public function getUnreadAttribute()
    {
        return $this->chats()->where('is_read',0)->count();
    }
}
