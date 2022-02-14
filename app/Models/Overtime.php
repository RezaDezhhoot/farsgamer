<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static findOrfail($id)
 * @method static where(string $string, $id)
 * @property mixed start_at
 * @property mixed end_at
 * @property mixed user_id
 * @property int|mixed|string|null manger
 */
class Overtime extends Model
{
    use HasFactory;

    public function mangers()
    {
        return $this->belongsTo(User::class,'manger','id');
    }
}
