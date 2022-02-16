<?php

namespace App\Http\Livewire\Site\Dashboard\Others;

use App\Http\Livewire\BaseComponent;
use App\Cart\Cart;

class IndexSaved extends BaseComponent
{
    public function render()
    {
        $saved = Cart::content('saved');
        return view('livewire.site.dashboard.others.index-saved',['saved' => $saved]);
    }
}
