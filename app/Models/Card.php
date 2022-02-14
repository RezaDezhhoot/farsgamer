<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Self_;

/**
 * @method static findOrFail($id)
 * @method static lateast(string $string)
 * @method static latest(string $string)
 * @method static where(string $string, string $NOT_CONFIRMED)
 */
class Card extends Model
{
    use HasFactory;

    const CONFIRMED = 'confirmed' , NOT_CONFIRMED = 'not_confirmed' , NEW = 'new';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getStatus()
    {
        return [
            self::NEW => 'جدید',
            self::CONFIRMED => 'تایید شده ',
            self::NOT_CONFIRMED => 'تایید نشده ',
        ];
    }

    public static function bank()
    {
        return  [
            'sepah' => 'سپه',
            'saman' => 'سامان',
            'melat' => 'ملت',
            'meli' => 'ملی',
            'tejarat' => 'تجارت',
            'sina' => 'سینا',
            'shahr' => 'شهر',
            'saderat' => 'صادرات',
            'keshavarzi' => 'کشاورزی',
            'parsian' => 'پازسان',
            'pasargad' => 'پاسارگاد',
            'sarmaye' => 'سرمایه',
        ];
    }

    public static function getNew()
    {
        return Card::where('status',self::NEW)->count();
    }
}
