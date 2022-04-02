<?php

namespace App\Models;

/**
 * @property mixed name
 * @method static insert(array $data)
 */
class Permission extends \Spatie\Permission\Models\Permission
{

    protected static $logAttributes = ['*'];
    protected static $dontLogIfAttributesChangedOnly = ['updated_at'];
    protected static $logOnlyDirty = true;

    protected $fillable = ['name','guard_name'];
    protected $attributes = [
        'guard_name' => 'web',
    ];

    public function lang()
    {
        return [
            'show' => 'نمایش',
            'edit' => 'ویرایش',
            'delete' => 'حذف',
            'dashboard' => 'داشبورد',
            'users' => 'کاربران',
            'cards' => 'حساب های بانکی',
            'roles' => 'نقش ها',
            'tasks' => 'وظایف',
            'platforms' => 'پلتفرم ها',
            'categories' => 'دسته بندی ها',
            'addresses' => 'ادرس ها',
            'articles' => 'مقالات',
            'article_categories' => 'دسته بندی مقالات',
            'sends' => 'حمل نقل',
            'requests' => 'درخواست ها',
            'payments' => 'پرداخت ها',
            'securities' => 'امنیت',
            'settings_base' => 'تنطیمات پایه',
            'settings_home' => 'تنطیمات صفحه اصلی',
            'settings_aboutUs' => 'تنطیمات درباره ما',
            'settings_contactUs' => 'تنطیمات ارتباط با ما',
            'settings_law' => 'تنطیمات قوانین',
            'settings_chatLaw' => 'تنطیمات قوانین چت',
            'settings_fag' => 'تنطیمات سوالات متداول ',
            'settings_transaction' => 'تنطیمات معاملات  ',
            'settings' => 'تنطیمات',
            'orders' => 'سفارش ها',
            'chats' => 'چت ها',
            'tickets' => 'تیکت ها',
            'notifications' => 'اعلان ها',
            'comments' => 'کامنت ها',
            'transactions' => 'معاملات',
            'cancel' => 'لغو',
            'offends' => 'تخلفات',
            'reports'=> 'گزارش ها',
        ];
    }

    public function getLabelAttribute()
    {
        $action = '';
        $names = explode('_',$this->name);
        if (in_array(str_replace($names[0].'_','',$this->name),array_keys($this->lang()))){
            $action = $this->lang()[$names[0]];
            $model = '';
            $model = $this->lang()[str_replace($names[0].'_','',$this->name)];
            return $action.' '.$model;
        }
        return  $this->name;
    }

}
