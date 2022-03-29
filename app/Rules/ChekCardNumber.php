<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ChekCardNumber implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $irCard = true;
        $card = (string) preg_replace('/\D/','',$value);
        $strlen = strlen($card);
        if($irCard==true and $strlen!=16)
            return false;
        if($irCard!=true and ($strlen<13 or $strlen>19))
            return false;
        if(!in_array($card[0],[2,4,5,6,9]))
            return false;

        $res = [];
        for($i=0; $i<$strlen; $i++)
        {
            $res[$i] = $card[$i];
            if(($strlen%2)==($i%2))
            {
                $res[$i] *= 2;
                if($res[$i]>9)
                    $res[$i] -= 9;
            }
        }
        return array_sum($res)%10==0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'شماره کارت نامعتبر می باشد.';
    }
}
