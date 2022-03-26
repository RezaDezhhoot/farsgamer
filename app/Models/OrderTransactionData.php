<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, $id)
 * @method static updateOrCreate(string[] $array, array $array1)
 * @property mixed order_transaction_id
 * @property mixed|string name
 */
class OrderTransactionData extends Model
{
    use HasFactory;

    protected $table = 'orders_transaction_data';

    protected $fillable = ['updated_at','order_transaction_id','name'];

    protected $guarded = [];

    public function send()
    {
        return $this->belongsTo(Send::class)->withTrashed();;
    }
}
