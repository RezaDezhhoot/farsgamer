<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

/**
 * @method static findOrFail($id)
 * @method static where(array[] $array)
 * @property mixed created_at
 * @property mixed subject
 * @property mixed content
 * @property mixed type
 * @property mixed user_id
 * @property mixed model
 * @property mixed model_id
 */
class Notification extends Model
{
    use HasFactory;

    const PUBLIC = 'public' , PRIVATE = 'private';

    const TRANSACTION = 'OrderTransaction' , ORDER = 'Order' , AUTH = 'Auth' , ADDRESS = 'Address' , CARD = 'Card' , REQUEST = 'Request';
    const CHAT = 'Chat' , TICKET = 'Ticket' , ALL = 'All' , NEWS = 'News'  , SECURITY = 'Security' , User = 'User';

    public static function getSubject()
    {
        return [
            self::TRANSACTION => 'معاملات',
            self::ORDER => 'اگهی ها',
            self::AUTH => ' احراز هویت',
            self::User => 'حساب کاربری',
            self::ADDRESS => 'ادرس',
            self::CARD => 'حساب های بانکی',
            self::REQUEST => 'درخواست تسویه حساب',
            self::CHAT => 'چت',
            self::TICKET => 'تیکت',
            self::SECURITY => 'امنیتی',
            self::NEWS => 'اطلاع رسانی',
            self::ALL => 'عمومی',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSubjectLabelAttribute()
    {
        return self::getSubject()[$this->subject];
    }

    public static function getType()
    {
        return [
            self::PRIVATE => 'خصوصی',
            self::PUBLIC => 'عمومی',
        ];
    }

    public function getDateAttribute()
    {
        return Jalalian::forge($this->created_at)->format('%A, %d %B %Y');
    }
}
