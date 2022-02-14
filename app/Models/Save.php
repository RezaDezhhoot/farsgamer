<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, int|string|null $id)
 */
class Save extends Model
{
    use HasFactory;

    protected $table = 'saved';

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    protected $fillable = [
        'order_id',
        'user_id',
    ];
}
