<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;
use App\Traits\Admin\Searchable;

/**
 * @property mixed created_at
 * @property mixed status
 * @property mixed result
 * @property mixed file
 * @property mixed link
 * @property mixed track_id
 * @property bool|mixed backed
 * @property mixed user
 * @property mixed price
 * @method static latest(string $string)
 * @method static where(string $string, string $NEW)
 * @method static findOrFail($id)
 * @method static whereBetween(string $string, string[] $array)
 */
class Request extends Model
{
    use HasFactory , Searchable;

    protected $searchAbleColumns = ['track_id','id'];

    const SETTLEMENT = 'settlement' , REJECTED = 'rejected' , NEW = 'new';

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public static function getNew()
    {
        return Request::where('status',self::NEW)->count();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getStatus()
    {
        return[
            self::SETTLEMENT => 'واریز شده',
            self::REJECTED => 'رد شده',
            self::NEW => 'جدید',
        ];
    }

    public function getDateAttribute()
    {
        return Jalalian::forge($this->created_at)->format('%A, %d %B %Y');
    }
}
