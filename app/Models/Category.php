<?php

namespace App\Models;

use App\Traits\Admin\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, string $string1)
 * @method static latest(string $string)
 * @method static findOrFail($id)
 * @method static withCount(string $string)
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
 */
class Category extends Model
{
    use HasFactory , Searchable;

    protected $searchAbleColumns = ['title','slug'];

    const AVAILABLE = 'available' , UNAVAILABLE = 'unavailable';

    const YES = 'yes' , NO = 'no';

    const DIGITAL = 'digital' , PHYSICAL = 'physical';

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
            self::YES => ' قابل معامله',
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
