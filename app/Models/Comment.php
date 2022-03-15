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
 * @property mixed commentable_type
 */
class Comment extends Model
{
    use HasFactory;

    const CONFIRMED = 'confirmed' , UNCONFIRMED = 'unconfirmed' , NEW = 'new';

//    const ARTICLE = 'article' , USER = 'user';
    const ARTICLE = 'App\Models\Article' , USER = 'App\Models\User';
    public function user()
    {
        return $this->belongsTo(User::class);
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

    public function getStatusLabelAttribute()
    {
        return self::getStatus()[$this->status];
    }

    public static function getNew()
    {
        return Comment::where('status',self::NEW)->count();
    }

    public function commentable()
    {
        return $this->morphTo(__FUNCTION__, 'commentable_type', 'commentable_id');
    }

    public function getCommentableTypeLabelAttribute()
    {
        if ($this->commentable_type == self::ARTICLE) {
            return self::ARTICLE;
        } elseif ($this->commentable_type == self::USER) {
            return self::USER;
        }
    }

}
