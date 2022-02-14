@props(['id', 'label', 'type' , 'required' => false ,'disabled' => false ,'help' => false])
<div class="form-group">
    <label for="{{$id}}">{{$label}}</label>
    <input  {!! $attributes->merge(['class'=> 'form-control']) !!} {{ $disabled ? 'disabled' : '' }} type="{{$type}}" id="{{$id}}" {{ $attributes }}>
    @if($help)
        <small class="text-info">{{$help}}</small>
    @endif
</div>
