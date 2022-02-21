<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

/**
 * @property mixed status
 * @property mixed created_at
 * @method static create(array $array)
 */
class OrderTransactionPayment extends Model
{
    use HasFactory;
    protected $table = 'order_transactions_payments';
    protected $guarded = [];
    const SUCCESS = 'success' , FAILED = 'failed';

    public static function getStatus()
    {
        return [
            self::SUCCESS => 'موق',
            self::FAILED => 'ناموفق',
        ];
    }

    public function getStatusLabelAttribute()
    {
        return self::getStatus()[$this->status];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderTransaction()
    {
        return $this->belongsTo(OrderTransaction::class,'orders_transactions_id');
    }

    public function getDateAttribute()
    {
        return Jalalian::forge($this->created_at)->format('%A, %d %B %Y');
    }

}
