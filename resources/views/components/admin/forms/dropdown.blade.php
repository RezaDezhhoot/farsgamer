@props(['id', 'label' , 'data' , 'required' => false , 'help' => false , 'value' => false ])
<div class="form-group">
    <label for="{{$id}}"> {{$label}} </label>
    <select {{ $attributes }}  id="{{$id}}"  {!! $attributes->merge(['class'=> 'form-control']) !!}>
        <option value="">انتخاب</option>
        @foreach($data as $key => $item)
            <option value="{{ $value ? $item : $key }}">{{$item}}</option>
        @endforeach
    </select>
    @if($help)
        <small class="text-info">{{$help}}</small>
    @endif
</div>
