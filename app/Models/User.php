<?php

namespace App\Models;

use App\Traits\Admin\Searchable;
use Bavix\Wallet\Traits\CanPay;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Bavix\Wallet\Interfaces\Confirmable;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Models\Transaction;
use Bavix\Wallet\Traits\CanConfirm;
use Bavix\Wallet\Traits\HasWallet;
use Spatie\Permission\Traits\HasRoles;

/**
 * @method static latest(string $string)
 * @method static findOrFail($id)
 * @method static where(string $string, $username)
 * @method static find($receiver)
 * @method static orderBy(string $string)
 * @method static whereBetween(string $string, string[] $array)
 * @method static role(string $string)
 * @method static create(array $array)
 * @property mixed first_name
 * @property mixed last_name
 * @property mixed phone
 * @property mixed user_name
 * @property mixed province
 * @property mixed city
 * @property mixed status
 * @property mixed email
 * @property mixed code_id
 * @property mixed score
 * @property mixed description
 * @property mixed auth_image
 * @property mixed profile_image
 * @property mixed|string pass_word
 * @property mixed id
 * @property mixed full_name
 * @property mixed name
 * @property mixed|string password
 * @property mixed alerts
 * @property mixed cards
 * @property mixed overtimes
 * @property mixed ban
 */
class User extends Authenticatable implements Wallet, Confirmable
{
    use HasApiTokens, HasFactory, Notifiable , HasRoles , HasWallet, CanConfirm;
    use Searchable , SoftDeletes ;

    const DEFAULT_IMAGE = '/defaults/pngwing.com.png';

    const NOT_CONFIRMED = 'not_confirmed';
    const NEW = 'new';
    const CONFIRMED = 'confirmed';
    const WAIT_TO_CONFIRM = 'wait_for_confirm';

    protected $guarded = ['id'];

    protected $searchAbleColumns = ['user_name','phone'];

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public static function getStatus()
    {
        return [
            self::NEW => 'جدید',
            self::NOT_CONFIRMED => 'تایید نشده',
            self::CONFIRMED => 'تایید شده',
            self::WAIT_TO_CONFIRM => 'در انتظار تایید',
        ];
    }

    public function getStatusLabelAttribute()
    {
        return self::getStatus()[$this->status];
    }

    public function payments()
    {
        return $this->belongsTo(Payment::class);
    }

    public function getProvinceLabelAttribute()
    {
        return !empty($this->province) ? Setting::getProvince()[$this->province] : '';
    }


    public function getCityLabelAttribute()
    {
        return (!empty($this->province) && !empty($this->city)) ? Setting::getCity()[$this->province][$this->city] : '';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getBanedAttribute()
    {
        $ban = Carbon::make(now())->diff($this->ban,false)->format('%r%i');
        return  $ban > 0;
    }

    public function getFullNameAttribute()
    {
        return $this->name;
    }


    public function alerts()
    {
        return $this->hasMany(Notification::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }


    public static function getNew()
    {
        return User::where('status',self::NEW)->orWhere('status',self::NOT_CONFIRMED)->orWhere('status',self::WAIT_TO_CONFIRM)->count();
    }


    public function cards()
    {
        return $this->hasMany(Card::class);
    }


    public function schedule()
    {
        return $this->hasOne(Schedule::class);
    }

    public function overtimes(){
        return $this->hasMany(Overtime::class);
    }

    public function setProfileImageAttribute($value)
    {
        $this->attributes['profile_image'] = str_replace(env('APP_URL'), '', $value);
    }

    public function getProfileImageAttribute($value): string
    {
        return empty($value) ? env('APP_URL').self::DEFAULT_IMAGE : asset($value);
    }

    public function setAuthImageAttribute($value)
    {
        $this->attributes['auth_image'] = str_replace(env('APP_URL'), '', $value);
    }

    public function getInventoryBeingTradedAttribute()
    {
        return OrderTransaction::with('order:price')->where([
            ['status','!=',OrderTransaction::IS_REQUESTED],
            ['status','!=',OrderTransaction::IS_CANCELED],
            ['status','!=',OrderTransaction::WAIT_FOR_CONFIRM],
            ['status','!=',OrderTransaction::WAIT_FOR_PAY],
            ['status','!=',OrderTransaction::WAIT_FOR_COMPLETE],
        ])->where(function ($query){
            return $query->where('seller_id',auth()->id())->orWhere('customer_id',auth()->id());
        })->withSum('order','price')->get()->sum('order_sum_price');
    }



    public function getOrdersHasTransactionAttribute()
    {
        return OrderTransaction::where([
            ['status','!=',OrderTransaction::IS_REQUESTED],
            ['status','!=',OrderTransaction::IS_CANCELED],
            ['status','!=',OrderTransaction::WAIT_FOR_CONFIRM],
            ['status','!=',OrderTransaction::WAIT_FOR_COMPLETE],
            ['seller_id',auth()->id()]
        ])->count();
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }
}


