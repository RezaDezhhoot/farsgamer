<?php

namespace App\Http\Livewire\Site\Dashboard\Others;

use App\Http\Livewire\BaseComponent;

class IndexSaved extends BaseComponent
{
    public function render()
    {
        $user = auth()->user();
        $saved = $user->saves();
        return view('livewire.site.dashboard.others.index-saved',['saved' => $saved]);
    }
}
