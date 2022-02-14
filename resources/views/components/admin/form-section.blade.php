@props(['label'])
<div class="form-group">
    <p>
        {{$label}}
    </p>
    <div>
        {{ $slot }}
    </div>
</div>
