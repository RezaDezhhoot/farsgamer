<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Admin\Searchable;
use Illuminate\Support\Str;

/**
 * @method static where(string $string, $status)
 * @method static find(int|string $key)
 * @method static findOrFail($id)
 * @method static latest(string $string)
 * @method static withCount(string $string)
 */
class Platform extends Model
{
    use HasFactory , Searchable;
    protected $searchAbleColumns = ['slug'];

    public function orders()
    {
        return $this->belongsToMany(Order::class , 'orders_has_platforms' ,'platform_id' ,'order_id');
    }

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value);
    }

    public function setLogoAttribute($value)
    {
        $this->attributes['logo'] = str_replace(env('APP_URL'), '', $value);
    }
}
