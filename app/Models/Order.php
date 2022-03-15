<?php

namespace App\Models;

use App\Traits\Admin\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Morilog\Jalali\Jalalian;

/**
 * @method static latest(string $string)
 * @method static where(string $string, $status)
 * @method static findOrFail($id)
 * @method static count()
 * @method static find($id)
 * @method static whereBetween(string $string, string[] $array)
 * @property mixed status
 * @property mixed slug
 * @property mixed content
 * @property mixed category_id
 * @property mixed price
 * @property mixed image
 * @property mixed gallery
 * @property mixed province
 * @property mixed city
 * @property mixed id
 * @property mixed created_at
 */
class Order extends Model
{
    use HasFactory;
    use Searchable , SoftDeletes;

    const IS_UNCONFIRMED = 'unconfirmed' , IS_CONFIRMED = 'confirmed' ,  IS_NEW = 'new';
    const IS_REJECTED = 'rejected' , IS_REQUESTED = 'requested' , IS_FINISHED = 'finished';
    /**
     * @var mixed
     */

    protected $guarded = [];


    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value);
    }

    public function setImageAttribute($value)
    {
        $this->attributes['image'] = str_replace(env('APP_URL'), '', $value);
    }


    public function setGalleryAttribute($value)
    {
        $gallery = [];
        foreach (explode(',',$value) as $item)
            $gallery[] = str_replace(env('APP_URL'), '', $item);

        $this->attributes['gallery'] = implode(',',$gallery);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getProvinceLabelAttribute()
    {
        return $this->province ? Setting::getProvince()[$this->province] : '';
    }

    public function getCityLabelAttribute()
    {
        return ($this->province && $this->city) ?  Setting::getCity()[$this->province][$this->city] : '';
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getStatusLabelAttribute()
    {
        return self::getStatus()[$this->status];
    }

    public function platforms()
    {
        return $this->belongsToMany(Platform::class , 'orders_has_platforms' ,'order_id' ,'platform_id');
    }

    public function parameters()
    {
        return $this->belongsToMany(Parameter::class , 'orders_has_parameters' ,'order_id' ,'parameter_id');
    }

    public function saves()
    {
        return $this->hasMany(Save::class);
    }

    public static function getStatus()
    {
        return [
            self::IS_NEW => '  جدید',
            self::IS_UNCONFIRMED => 'تایید نشده',
            self::IS_CONFIRMED => 'تایید شده',
            self::IS_REJECTED => 'رد شده',
            self::IS_REQUESTED => 'در حال معامله',
            self::IS_FINISHED => 'معامله شده',
        ];
    }

    public function OrderTransactions()
    {
        return $this->hasMany(OrderTransaction::class);
    }

    public static function getNew()
    {
        return Order::where('status',self::IS_NEW)->count();
    }

    public function getDateAttribute()
    {
        return Jalalian::forge($this->created_at)->format('%A, %d %B %Y');
    }
}
