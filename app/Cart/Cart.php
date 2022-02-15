<?php


namespace App\Cart;


use Illuminate\Support\Facades\Session;

class Cart
{
    const LAST_VIEW = 'last_view' , SAVED = 'saved';


    public function disks()
    {
        return [
            self::LAST_VIEW , self::SAVED
        ];
    }

    public function add($disk,$order)
    {
        if (in_array($disk,$this->disks())){
            $content = $this->content($disk);
            $content->put($order->id, $order);
            session()->put($disk , $content);
        }
    }

    public function get($disk,$id)
    {
        $content = $this->content($disk);
        if (!$content->has($id))
            return (false);

        return $content->get($id);
    }


    public function delete($disk,$id)
    {
        $content = $this->content($disk);
        if (!empty($this->get($disk,$id))) {
            $cartItem = $this->get($disk,$id);
            $content->pull($cartItem->id);
            session()->put($disk, $content);
        }
    }

    public function content($disk)
    {
        return session()->has($disk) ? session($disk) : collect();
    }
}
