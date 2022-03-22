<?php

namespace App\Models;

use App\Traits\Admin\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @method static latest(string $string)
 * @method static findOrFail($id)
 * @method static where(string $string, string $AVAILABLE)
 * @method static active(bool $active)
 * @property mixed slug
 * @property mixed title
 * @property mixed logo
 * @property mixed|null parent_id
 * @property mixed status
 */
class ArticleCategory extends Model
{
    use HasFactory , Searchable;
    protected $table = 'articles_categories';
    protected $searchAbleColumns = ['title','slug'];
    const AVAILABLE = 'available' , UNAVAILABLE = 'unavailable';

    protected $guarded = [];

    public static function getStatus()
    {
        return [
            self::AVAILABLE => 'فعال',
            self::UNAVAILABLE => 'غیر فعال',
        ];
    }

    public function setLogoAttribute($value)
    {
        $this->attributes['logo'] = str_replace(env('APP_URL'), '', $value);
    }

    public function scopeActive($query , $active = true)
    {
        return $active ? $query->where('status',self::AVAILABLE) : $query;
    }

    public function getStatusLabelAttribute()
    {
        return self::getStatus()[$this->status];
    }

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value);
    }

    public function parent()
    {
        return $this->belongsTo(ArticleCategory::class,'parent_id');
    }
}
