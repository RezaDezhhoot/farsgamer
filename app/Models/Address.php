<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static latest(string $string)
 * @method static findOrFail($id)
 * @method static where(string $string, string $NEW)
 * @property mixed country
 * @property mixed province
 * @property mixed city
 * @property mixed address
 * @property mixed status
 */
class Address extends Model
{
    use HasFactory;

    const CONFIRMED = 'confirmed' , NOT_CONFIRMED  = 'not_confirmed' , NEW = 'new';

    protected $guarded = [];

    public  function getFullAddressAttribute()
    {
        return $this->country.' - '.$this->province.' - '.$this->city.' - '.$this->address;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getStatus()
    {
        return [
            self::CONFIRMED => 'تایید شده',
            self::NOT_CONFIRMED => 'تایید نشده',
            self::NEW => 'جدید',
        ];
    }

    public static function getNew()
    {
        return Address::where('status',self::NEW)->count();
    }

}
