<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpDocumentor\Reflection\Types\Self_;

/**
 * @method static findOrFail($id)
 * @method static lateast(string $string)
 * @method static latest(string $string)
 * @method static where(string $string, string $NOT_CONFIRMED)
 * @method static active(bool $active)
 * @method static create(array $data)
 * @method static updateOrCreate(array $key, array $value)
 * @property mixed status_label
 * @property mixed user_id
 * @property mixed status
 * @property mixed user
 * @property mixed id
 * @property mixed card_number
 * @property mixed card_sheba
 * @property mixed bank
 * @property mixed bank_logo
 */
class Card extends Model
{
    use HasFactory , SoftDeletes;

    const CONFIRMED = 'confirmed' , NOT_CONFIRMED = 'not_confirmed' , NEW = 'new';

    protected $guarded = [];

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

    public function getStatusLabelAttribute()
    {
        return self::getStatus()[$this->status];
    }

    public function getBankLabelAttribute()
    {
        return self::bank()[$this->bank];
    }

    public function scopeActive($query,$active = true)
    {
        return $active ? $query->where('status',self::CONFIRMED) : $query;
    }

    public static function bank()
    {
        return  [
            '603799' => 'ملی',
            '589210' => 'سپه',
            '627648' => 'صادرات',
            '627961' => 'صنعت و معدن',
            '603770' => 'کشاورزی',
            '628023' => 'مسکن',
            '627760' => 'پست بانک ایران',
            '502908' => 'توسعه تعاون',
            '627412' => 'اقتصاد نوینن',
            '622106' => 'پارسیان',
            '502229' => 'پاسارگاد',
            '627488' => 'کارآفرین',
            '621986' => 'سامان',
            '639346' => 'سینا',
            '639607' => 'سرمایه',
            '636214' => 'تات',
            '502806' => 'شهر',
            '502938' => 'دی',
            '603769' => 'صادرات',
            '610433' => 'ملت',
            '627353' => 'تجارت',
            '589463' => 'رفاه',
            '627381' => 'انصار',
            '639370' => 'اقتصاد',
        ];
    }

    public static function getNew()
    {
        return Card::where('status',self::NEW)->count();
    }
}
