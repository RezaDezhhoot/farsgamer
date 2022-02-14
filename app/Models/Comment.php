<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static latest(string $string)
 * @method static findOrFail($id)
 * @method static where(string $string, string $CONFIRMED)
 * @property mixed type
 * @property mixed status
 */
class Comment extends Model
{
    use HasFactory;

    const CONFIRMED = 'confirmed' , UNCONFIRMED = 'unconfirmed' , NEW = 'new';

    const ARTICLE = 'article' , USER = 'user';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTargetAttribute()
    {
        return $this->type == 'article' ?
            $this->belongsTo(Article::class,'case_id')->first()->slug ?? '-':
            $this->belongsTo(User::class,'case_id')->first()->user_name ?? '-';
    }

    public static function getStatus()
    {
        return [
            self::CONFIRMED => 'تایید شده',
            self::UNCONFIRMED => 'تایید نشده',
            self::NEW => 'جدید',
        ];
    }

    public static function getFor()
    {
        return [
            self::ARTICLE => 'مقالات',
            self::USER => 'کاربران',
        ];
    }

    public function getForAttribute()
    {
        return self::getFor()[$this->type];
    }
    public function getStatusLabelAttribute()
    {
        return self::getStatus()[$this->status];
    }

    public static function getNew()
    {
        return Comment::where('status',self::NEW)->count();
    }

}
