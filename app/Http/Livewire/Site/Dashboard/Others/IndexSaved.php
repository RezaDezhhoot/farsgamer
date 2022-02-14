<?php

namespace App\Http\Livewire\Site\Dashboard\Others;

use Livewire\Component;

class IndexSaved extends Component
{
    public function render()
    {
        $user = auth()->user();
        $saved = $user->saves();
        return view('livewire.site.dashboard.others.index-saved',['saved' => $saved]);
    }
}
