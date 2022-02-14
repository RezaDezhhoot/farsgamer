<?php

namespace App\Models;

use App\Traits\Admin\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static latest(string $string)
 * @method static findOrFail($id)
 * @method static where(string $string, string $AVAILABLE)
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
    public static function getStatus()
    {
        return [
            self::AVAILABLE => 'فعال',
            self::UNAVAILABLE => 'غیر فعال',
        ];
    }
    public function parent()
    {
        return $this->belongsTo(ArticleCategory::class,'parent_id');
    }
}
