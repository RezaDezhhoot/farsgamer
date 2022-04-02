<?php

namespace App\Models;

use App\Traits\Admin\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static latest(string $string)
 * @method static findOrFail($id)
 * @method static where(string $string, $task)
 * @property mixed name
 * @property array|mixed task
 * @property mixed where
 * @property mixed value
 */
class Task extends Model
{
    use HasFactory ,Searchable;
    protected $searchAbleColumns = ['name'];

    public static function event()
    {
        return [
            'sms' => 'SMS',
            'email' => 'EMAIL',
            'notification' => 'NOTIFICATION',
            'sms_email' => 'SMS & EMAIL',
            'sms_notification' => 'SMS & NOTIFICATION',
            'email_notification' => 'EMAIL & NOTIFICATION',
            'sms_email_notification' => 'SMS & EMAIL & NOTIFICATION',
        ];
    }

    public function getTaskLabelAttribute()
    {
        return self::tasks()[$this->where];
    }

    public function getWhereLabelAttribute()
    {
        return self::event()[$this->task];
    }

    public static function tasks()
    {
        return [
            'new_order' => 'اکهی جدید',#ok
            'confirm_order' => 'تایید اگهی',#ok
            'reject_order' => 'رد اگهی',#ok
            'delete_order' => 'حذف اگهی',#ok
            'confirm_transaction' => 'در انتظار تایید توسط فروشنده',#ok
            'pay_transaction' => 'در انتظار پرداخت ',#ok
            'send_transaction' => 'در انتظار ارسال محصول مورد نظر توسط فروشنده',#ok
            'receive_transaction' => 'در انتظار دریافت محصول مورد نظر توسط خریدار',#ok
            'no_receive_transaction' => 'عدم دریافت محصول مورد نظر توسط خریدار',#ok
            'complete_transaction' => 'تکمیل معامله',#ok
            'request_to_return_transaction' => 'درخواست مرجوع کردن محصول(مرجوعی)',#ok
            'confirm_returned_transaction' => 'تایید مرجوع شدن محصول(مرجوعی)',#ok
            'reject_returned_transaction' => 'رد مرجوع شدن محصول(مرجوعی)',#ok
            'send_data_transaction' => 'در انتظار ارسال اطلاعات توسط فروشنده(مرجوعی)',#ok
            'returned_send_transaction' => 'در انتظار ارسال توسط خریدار(مرجوعی)',#ok
            'returned_receive_transaction' => 'در انتظار دریافت توسط فروشنده(مرجوعی)',#ok
            'return_no_receive_transaction' => 'عدم دریافت محصول مورد نظر توسط فروشنده(مرجوعی)',#ok
            'cancel_transaction' => 'لفو معامله',#ok
            'control_data' => 'کنترل توسط واسطه',#ok
            'skip_step' => 'رد شدن از مراحل توسط طرف معامله بابت دیر کرد',
            'confirm_card' => 'تایید حساب بانکی',#ok
            'reject_card' => 'رد حساب بانکی',#ok
            'delete_card' => 'حذف بانکی',#ok
            'login' => 'ورود به حساب کاربری',
            'signUp'=> 'ثبت نام',
            'auth'=> 'تکمیل احراز هویت',#ok
            'not_confirmed' => 'رد حساب کاربری',#ok
            'settlement_request' => 'درخواست واریز',#ok
            'complete_request' => 'انجام واریز مبلغ',#ok
            'rejected_request' => 'عدم واریز هزینه',#ok
            'new_ticket' => 'تیکت جدید',#ok
            'ticket_answer' => 'پاسخ تیکت',#ok
            'new_message' => 'پیام جدید',
            'baned_user' => 'بلاک شدن کاربر',
            'pay' => 'پرداخت',
        ];
    }
}
