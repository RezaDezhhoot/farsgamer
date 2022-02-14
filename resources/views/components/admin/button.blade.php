@props(['content' , 'class' ,'style' => false])
<div>
    <div class="form-group">
        <button type="button" {{ $attributes }} style="{{ $style }}" class="btn btn-{{$class}}">
            {{$content}}
        </button>
    </div>
</div>
