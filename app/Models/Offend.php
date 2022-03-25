<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

/**
 * @property mixed created_at
 * @method static create(array $data)
 * @method static latest(string $string)
 */
class Offend extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getDateAttribute()
    {
        return Jalalian::forge($this->created_at)->format('%A, %d %B %Y');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
