<?php

namespace App\Models;

use App\Traits\Admin\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Setting;
use Illuminate\Support\Carbon;
use Morilog\Jalali\Jalalian;

/**
 * @method static latest(string $string)
 * @method static findOrFail($id)
 * @method static count()
 * @method static where(string $string, $status)
 * @method static whereBetween(string $string, string[] $array)
 * @method static create(array $array)
 * @property mixed customer_id
 * @property mixed seller_id
 * @property mixed created_at
 * @property mixed timer
 * @property mixed commission
 * @property mixed intermediary
 * @property mixed id
 * @property mixed seller
 * @property mixed order_id
 * @property mixed|string status
 * @property mixed is_returned
 */
class OrderTransaction extends Model
{
    protected $dates = [
        'timer','created_at','updated_at'
    ];

    protected $guarded;

    use HasFactory , Searchable;
    const PREFIX = 'FG-';

    protected $table = 'orders_transactions';

    const IS_REQUESTED = 'requested';
    const WAIT_FOR_CONFIRM = 'wait_for_confirm';
    const WAIT_FOR_PAY = 'wait_for_pay';
    const WAIT_FOR_SEND = 'wait_for_send';
    const WAIT_FOR_RECEIVE = 'wait_for_receive';
    const WAIT_FOR_NO_RECEIVE = 'not_received';
    const WAIT_FOR_TESTING = 'wait_for_testing';
    const WAIT_FOR_COMPLETE = 'completed';
    const IS_RETURNED = 'returned';
    const IS_CANCELED = 'canceled';
    const WAIT_FOR_SENDING_DATA = 'wait_for_sending_data';
    const WAIT_FOR_CONTROL = 'wait_for_control';

    public function getCodeAttribute()
    {
        return self::PREFIX.$this->id;
    }

    public static function getFor()
    {
        return [
            'seller' => 'فروشنده',
            'customer' => 'خریدار',
        ];
    }

    public function getStatusLabelAttribute()
    {
        return self::getStatus($this->is_returned)[$this->status]['label'];
    }

    public function getProgressAttribute()
    {
        return self::getStatus($this->is_returned)[$this->status]['progress'];
    }

    public function seller()
    {
        return $this->belongsTo(User::class,'seller_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class , 'customer_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class , 'order_id')->withTrashed();
    }

    public function getTimeAttribute()
    {
        return Carbon::make(now())->diff($this->timer,false)->format('%r%d d %H : %i : %s');
    }

    public function payment()
    {
        return $this->hasOne(OrderTransactionPayment::class,'orders_transactions_id');
    }

    public static function getTimer(string $status)
    {
        $timer =  Setting::getSingleRow($status.'Timer') ?? 0;
        return $timer ? $timer : 0;
    }

    public function data()
    {
        return $this->hasOne(OrderTransactionData::class,'order_transaction_id');
    }

    public static function standardStatus()
    {
        return [
            self::WAIT_FOR_CONFIRM =>
                ['label' => 'در انتظار تایید درخواست' , 'icon' => 'flaticon2-accept icon-xl' , 'step' => 1,
                    'desc' => 'تایید درخواست از طرف فروشنده','timer' => self::getTimer(self::WAIT_FOR_CONFIRM)],
            self::WAIT_FOR_PAY =>
                ['label' => 'در انتظار پرداخت ' ,  'icon' => 'fas fa-money-bill-alt icon-xl'  , 'step' => 2,
                    'desc' => 'پرداخت از طرف خریدار','timer' => self::getTimer(self::WAIT_FOR_PAY)],
            self::WAIT_FOR_SEND =>
                ['label' => 'در انتظار ارسال' , 'icon' => 'fas fa-exchange-alt icon-xl'  , 'step' => 3,
                    'desc' => 'ارسال از طرف فروشنده','timer' => self::getTimer(self::WAIT_FOR_SEND)],
            self::WAIT_FOR_CONTROL =>
                ['label' => 'کنترل اطلاعات' , 'icon' => 'fas fa-check-double icon-xl'  , 'step' => 4,
                    'desc' => 'کنترل اطلاعات','timer' => 0],
            self::WAIT_FOR_RECEIVE =>
                ['label' => 'در انتظار دریافت' ,'icon' => 'fab fa-get-pocket icon-xl'  , 'step' => 5,
                    'desc' => 'دریافت از طرف خریدار', 'timer' => self::getTimer(self::WAIT_FOR_RECEIVE)],
            self::WAIT_FOR_NO_RECEIVE =>
                ['label' => 'دریافت نشده' ,'icon' => 'flaticon2-delete icon-xl' , 'step' => 0,
                    'desc' => 'دریافت نشده از طرف خریدار', 'timer' => 0],
            self::IS_RETURNED =>
                ['label' => 'درخواست مرجوعیت' ,'icon' => 'flaticon2-refresh-arrow icon-xl'  , 'step' => 6,
                    'desc' => 'درخواست مرجوعیت توسط خریدار','timer' => 0],
            self::WAIT_FOR_COMPLETE =>
                ['label' => 'تکمیل شده' ,'icon' => 'fas fa-check-circle icon-xl'  , 'step' => 7,
                    'desc' => 'تکمیل شده','timer' => self::getTimer(self::WAIT_FOR_COMPLETE)],
        ];
    }

    public static function returnedStatus()
    {
        return [
            self::WAIT_FOR_SENDING_DATA => ['label' => 'در انتظار ارسال اطلاعات' , 'icon' => 'flaticon2-accept icon-xl' , 'step' => 1,
                'desc' => 'در انتظار ارسال اطلاعات توسط فروشنده','progress' => 25 , 'color' => 'link' , 'timer' => self::getTimer(self::WAIT_FOR_SENDING_DATA)],
            self::WAIT_FOR_SEND => ['label' => 'در انتظار ارسال' ,'progress' => 50 , 'icon' => 'fas fa-exchange-alt icon-xl' , 'step' => 2,
                'desc' => 'در انتظار ارسال توسط خریدار', 'color' => 'link' , 'timer' => self::getTimer(self::WAIT_FOR_SEND)],
            self::WAIT_FOR_RECEIVE => ['label' => 'در انتظار دریافت ' , 'progress' => 75 ,'icon' => 'fab fa-get-pocket icon-xl'  , 'step' => 3,
                'desc' => 'در انتظار دریافت توسط فروشنده', 'color' => 'link' , 'timer' => self::getTimer(self::WAIT_FOR_RECEIVE)],
            self::WAIT_FOR_NO_RECEIVE => ['label' => 'دریافت نشده '  , 'color' => 'link' , 'progress' => 75 , 'icon' => 'fab fa-get-pocket icon-xl'  , 'step' => 3,
                'desc' => 'دریافت نشده از طرف فروشنده', 'timer' => self::getTimer(self::WAIT_FOR_RECEIVE)],
//            self::WAIT_FOR_COMPLETE => ['label' => 'تکمیل شده' , 'progress' => 100 , 'color' => 'link', 'step' => 4,'icon' => 'fas fa-check-circle icon-xl',
//                    'timer' => self::getTimer(self::WAIT_FOR_COMPLETE), 'desc' => 'تکمیل شده',],
            self::IS_CANCELED => ['label' =>'تکمیل مرجوعیت', 'color' => 'link' , 'progress' => 100 , 'icon' => 'flaticon2-cancel icon-xl'  , 'step' => 4,
                'desc' => 'تکمیل مرجوعیت مرجوعیت','timer' => self::getTimer(self::IS_CANCELED)],
        ];
    }

    public static function getStatus($return = 0)
    {
        if ($return == 1){
            return self::returnedStatus();
        }
        return [
            self::IS_REQUESTED =>
                ['label' => 'درخواست شده' , 'progress' => 10 , 'color' => 'link' , 'step' => 1,
                    'timer' => self::getTimer(self::IS_REQUESTED)],
            self::WAIT_FOR_CONFIRM =>
                ['label' => 'در انتظار تایید توسط فروشنده' , 'progress' => 20 , 'color' => 'link' , 'step' => 2,
                    'timer' => self::getTimer(self::WAIT_FOR_CONFIRM)],
            self::WAIT_FOR_PAY =>
                ['label' => 'در انتظار پرداخت ' , 'progress' => 35 , 'color' => 'link'  , 'step' => 3,
                    'timer' => self::getTimer(self::WAIT_FOR_PAY)],
            self::WAIT_FOR_SEND =>
                ['label' => 'در انتظار ارسال' , 'progress' => 50 , 'color' => 'link' , 'step' => 4,
                    'timer' => self::getTimer(self::WAIT_FOR_SEND)],
            self::WAIT_FOR_CONTROL =>
                ['label' => 'کنترل اطلاعات' , 'progress' => 70 ,  'color' => 'link' , 'step' => 5,],
            self::WAIT_FOR_RECEIVE =>
                ['label' => 'در انتظار دریافت' , 'progress' => 80 , 'color' => 'link' , 'step' => 6,
                    'timer' => self::getTimer(self::WAIT_FOR_RECEIVE)],
            self::WAIT_FOR_NO_RECEIVE =>
                ['label' => 'دریافت نشده' , 'progress' => 70 , 'color' => 'link' , 'step' => 6,
                     'timer' => 0],
            self::WAIT_FOR_COMPLETE =>
                ['label' => 'تکمیل شده' , 'progress' => 100 , 'color' => 'link', 'step' => 8,
                    'timer' => self::getTimer(self::WAIT_FOR_COMPLETE)],
            self::IS_RETURNED =>
                ['label' => 'مرجوعی' , 'progress' => 0 , 'color' => 'link' , 'step' => 0,
                    'timer' => self::getTimer(self::IS_RETURNED)],
            self::IS_CANCELED =>
                ['label' => 'لغو شده' , 'progress' => 0 , 'color' => 'link' , 'step' => 0,
                    'timer' => self::getTimer(self::IS_CANCELED)],
        ];
    }

    public static function receiveStatus()
    {
        return [
            0 => 'در انتطار دریافت ',
            1 => 'دریافت شده',
            2 => 'عدم دریافت در زمان مقرر از طرف فروشنده/خریدار',
        ];
    }

    public function getCategoryAttribute()
    {
        return $this->order()->first()->category;
    }

    public function getDateAttribute()
    {
        return Jalalian::forge($this->created_at)->format('%A, %d %B %Y');
    }

    public function getChatBetweenCustomerAndSeller()
    {
        return ChatGroup::where(function ($query){
            return $query->where('user1',$this->seller_id)->orWhere('user2',$this->seller_id);
        })->where(function ($query) {
            if ($this->customer_id == auth()->id())
                return $query->whereColumn('user1', 'user2');
            else
                return $query->where('user1',$this->customer_id)->orWhere('user2',$this->customer_id)->first();
        })->first();
    }

    public function comment()
    {
        return $this->hasOne(Comment::class,'order_transaction_id');
    }

    public function getPriceAttribute()
    {
        $commission = $this->commission;
        $intermediary = $this->intermediary;
        $price = $this->order->price + $commission/2 + $intermediary/2;

        return $price;
    }
}
