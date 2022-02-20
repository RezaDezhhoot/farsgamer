<?php

namespace App\Models;

use App\Traits\Admin\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

/**
 * @method static latest(string $string)
 * @method static findOrFail($id)
 * @method static create(array $array)
 * @method static whereBetween(string $string, string[] $array)
 * @method static whereNotNull(string $string)
 * @method static where(string $string, string $string1)
 * @method static find($code)
 * @property mixed created_at
 */
class Payment extends Model
{
    use HasFactory , Searchable;

    protected $guarded = [];

    public static function getStatus()
    {
        return [
            '100' => 'موق',
            '8' => 'به درگاه پرداخت منتقل شد',
            '10' => 'در انتظار تایید پرداخت',
        ];
    }



    protected $searchAbleColumns = ['payment_token'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDateAttribute()
    {
        return Jalalian::forge($this->created_at)->format('%A, %d %B %Y');
    }
}
