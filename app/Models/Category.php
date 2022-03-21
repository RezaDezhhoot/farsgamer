<?php

namespace App\Models;

use App\Traits\Admin\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @method static where(string $string, string $string1)
 * @method static latest(string $string)
 * @method static findOrFail($id)
 * @method static withCount(string $string)
 * @method static find(int[] $array)
 * @method static fineMany(array $sub_categories_id)
 * @method static findMany(array $sub_categories_id)
 * @method static active(bool $active)
 * @property mixed title
 * @property mixed slug
 * @property mixed logo
 * @property mixed default_image
 * @property mixed slider
 * @property mixed description
 * @property mixed seo_keywords
 * @property mixed seo_description
 * @property int|mixed send_time
 * @property mixed guarantee_time
 * @property mixed parent_id
 * @property mixed status
 * @property mixed commission
 * @property mixed is_available
 * @property mixed type
 * @property mixed control
 * @property false|mixed|string forms
 * @property mixed id
 * @property int|mixed receive_time
 * @property int|mixed sending_data_time
 * @property int|mixed pay_time
 * @property int|mixed no_receive_time
 * @property mixed intermediary
 */
class Category extends Model
{
    use HasFactory , Searchable , SoftDeletes;

    protected $searchAbleColumns = ['title','slug'];

    const AVAILABLE = 'available' , UNAVAILABLE = 'unavailable';

    const YES = 'yes' , NO = 'no';

    const DIGITAL = 'digital' , PHYSICAL = 'physical';


    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value);
    }

    public function getStatusLabelAttribute()
    {
        return self::getStatus()[$this->status];
    }

    public function setLogoAttribute($value)
    {
        $this->attributes['logo'] = str_replace(env('APP_URL'), '', $value);
    }

    public function scopeActive($query, $active = false)
    {
        return $active ? $query->where('status',self::AVAILABLE) : $query;
    }

    public function setSliderAttribute($value)
    {
        $this->attributes['slider'] = str_replace(env('APP_URL'), '', $value);
    }

    public function setDefaultImageAttribute($value)
    {
        $this->attributes['default_image'] = str_replace(env('APP_URL'), '', $value);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class,'parent_id');
    }

    public static function getStatus()
    {
        return [
            self::AVAILABLE => 'فعال',
            self::UNAVAILABLE => 'غیر فعال',
        ];
    }

    public static function available()
    {
        return [
            self::YES => 'قابل معامله',
            self::NO => 'غیر قابل معامله',
        ];
    }

    public static function type()
    {
        return [
            self::PHYSICAL => 'محصول فیزیکی',
            self::DIGITAL => 'محصول دیجیتالی',
        ];
    }

    public function sends()
    {
        return $this->belongsToMany(\App\Models\Send::class,'category_has_sends','category_id','send_id');
    }

    public function parameters()
    {
        return $this->hasMany(Parameter::class);
    }

    public function platforms()
    {
        return $this->belongsToMany(Platform::class,'category_has_platform','category_id','platform_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function child()
    {
        return $this->hasMany(Category::class,'parent_id');
    }

    public function childrenRecursive()
    {
        return $this->child()->with('childrenRecursive');
    }

    public function getTypeLabelAttribute()
    {
        return self::type()[$this->type];
    }
}
