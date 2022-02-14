<?php

namespace App\Http\Livewire;

use Livewire\Component;

class BaseComponent extends Component
{
    public function emitNotify($title, $icon = 'success')
    {
        $data['title'] = $title;
        $data['icon'] = $icon;

        $this->emit('notify', $data);
    }

    public function emitShowModal($id)
    {
        $this->emit('showModal', $id);
    }
    public function emitHideModal($id)
    {
        $this->emit('hideModal', $id);
    }
}
