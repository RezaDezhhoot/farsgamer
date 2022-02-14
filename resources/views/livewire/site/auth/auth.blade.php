<div>
    @if(\App\Http\Livewire\Site\Auth\Auth::MODE_LOGIN == $mode)
        @include('livewire.site.auth.login')
    @elseif(\App\Http\Livewire\Site\Auth\Auth::MODE_REGISTER == $mode)
        @include('livewire.site.auth.register')
    @elseif(\App\Http\Livewire\Site\Auth\Auth::MODE_VERIFY == $mode)
        @include('livewire.site.auth.verify')
    @endif
</div>
