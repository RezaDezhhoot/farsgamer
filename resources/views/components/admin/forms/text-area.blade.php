@props(['id', 'label' , 'disabled' => false ,'dir' => 'rtl' , 'help' => false])
<div class="form-group">
    <div style="padding: 5px">
        <label for="{{$id}}">{{$label}} </label>
        <textarea {{ $attributes->wire('model') }} dir="{{$dir}}" {{ $disabled ? 'disabled' : '' }} id="{{$id}}" class="resizable_textarea form-control"></textarea>
        @if($help)
            <small class="text-info">{{$help}}</small>
        @endif
    </div>
</div>
