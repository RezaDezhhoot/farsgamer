<?php

namespace App\Models;

use App\Traits\Admin\Searchable;
use Bavix\Wallet\Traits\CanPay;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
 */
class User extends Authenticatable implements Wallet, Confirmable
{
    use HasApiTokens, HasFactory, Notifiable , HasRoles , HasWallet, CanConfirm;
    use Searchable;


    const NOT_CONFIRMED = 'not_confirmed';
    const NEW = 'new';
    const CONFIRMED = 'confirmed';

    protected $searchAbleColumns = ['user_name','phone'];

    public static function getStatus()
    {
        return [
            self::NEW => 'جدید',
            self::NOT_CONFIRMED => 'تایید نشده',
            self::CONFIRMED => 'تایید شده',
        ];
    }

    public function getStatusLabelAttribute()
    {
        return self::getStatus()[$this->status];
    }

    public function getProvinceLabelAttribute()
    {
        return Setting::getProvince()[$this->province];
    }

    public function getCityLabelAttribute()
    {
        return Setting::getCity()[$this->province][$this->city];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'user_name',

        'email',
        'province',
        'city',
        'phone',
        'status',
        'pass_word',
        'ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name .' ' . $this->last_name;
    }


    public function alerts()
    {
        return $this->hasMany(Notification::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function saves()
    {
        return $this->hasMany(Save::class);
    }
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    public function contacts()
    {
        return ChatGroup::where(function ($query){
            return $query->where('user1',auth()->id())->orWhere('user2',auth()->id());
        });
    }

    public static function getNew()
    {
        return User::where('status',self::NEW)->orWhere('status',self::NOT_CONFIRMED)->count();
    }

    public function singleContact($id)
    {
        return $this->contacts()->where(function ($query) use ($id) {
            if ($id == auth()->id())
                return $query->whereColumn('user1', 'user2');
            else
                return $query->where('user1',$id)->orWhere('user2',$id)->first();
        })->first();
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    public function startChatWith($id , $is_admin = 0)
    {
        if ($id == auth()->id())
            return(false);

        $group = ChatGroup::where(function ($query){
            $query->where('user1',auth()->id())->orWhere('user2',auth()->id());
        })->where(function ($query) use ($id){
            $query->where('user1',$id)->orWhere('user2',$id);
        })->first();
        if (is_null($group)) {
            $group = new ChatGroup();
            $group->slug = 'chat'.uniqid();
            $group->user1 = auth()->id();
            $group->user2 = $id;
            $group->status = ChatGroup::OPEN;
            $group->is_admin = $is_admin;
            $group->save();
        }
        return $group->id;
    }

    public function schedule()
    {
        return $this->hasOne(Schedule::class);
    }

    public function overtimes(){
        return $this->hasMany(Overtime::class);
    }
}


