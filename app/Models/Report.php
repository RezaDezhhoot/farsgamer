<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, string $NEW)
 * @method static create(array $array)
 * @method static paginate(int $int)
 * @property mixed subject
 */
class Report extends Model
{
    use HasFactory;

    protected $guarded = [];

    const NEW = 'new',CHECKED = 'checked' , PROBLEM = 'problem';

    const CREATED = 'created' , UPDATED = 'updated' , DELETED = 'deleted';

    public function getStatus()
    {
        return [
            self::NEW => 'جدید',
            self::CHECKED => 'بررسی شده',
            self::PROBLEM => 'مشکل',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class,'actor_id');
    }

    public function getSubjectLabelAttribute()
    {
        return Notification::getSubject()[in_array($this->subject,Notification::getSubject()) ? $this->subject : Notification::ALL];
    }

    public static function getNew()
    {
        return Report::where('status',self::NEW)->count();
    }
}
