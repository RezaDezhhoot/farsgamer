<?php

namespace App\Models;

use App\Traits\Admin\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static findOrFail($id)
 * @method static latest(string $string)
 * @method static active(bool $active)
 * @property mixed slug
 * @property mixed title
 * @property mixed|string main_image
 * @property mixed content
 * @property mixed seo_keywords
 * @property mixed seo_description
 * @property int|mixed score
 * @property mixed status
 * @property int|mixed commentable
 * @property int|mixed google_indexing
 * @property int|mixed|string|null user_id
 * @property mixed status_label
 */
class Article extends Model
{
    use HasFactory , Searchable;

    protected $guarded = [];

    protected $searchAbleColumns = ['title','slug'];

    const SHARED = 'shared';
    const DEMO = 'demo';

    public static function getStatus()
    {
        return [
            self::SHARED => 'منتشر شده',
            self::DEMO => 'پیشنویش',
        ];
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }


    public function author()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value);
    }

    public function scopeActive($query , $active = true)
    {
        return $active ? $query->where('status',self::SHARED) : $query;
    }

    public function getStatusLabelAttribute()
    {
        return self::getStatus()[$this->status];
    }

    public function setMainImageAttribute($value)
    {
        $this->attributes['main_image'] = str_replace(env('APP_URL'), '', $value);
    }

    public function categories()
    {
        return $this->belongsToMany(ArticleCategory::class,'articles_has_categories','article_id','article_category_id')->withTrashed();
    }

}
