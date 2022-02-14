<?php

namespace App\Models;

use App\Traits\Admin\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static findOrFail($id)
 * @method static latest(string $string)
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
 */
class Article extends Model
{
    use HasFactory , Searchable;

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

    public function author()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function categories()
    {
        return $this->belongsToMany(ArticleCategory::class,'articles_has_categories','article_id','article_category_id');
    }

}
