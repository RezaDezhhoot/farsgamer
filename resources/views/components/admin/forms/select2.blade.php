@props(['id', 'label','data', 'value' => false])
<div class="form-group" wire:ignore>
    <label for="{{$id}}"> {{$label}} </label>
    <select  id="{{$id}}" {{ $attributes->wire('model') }}
            class="form-control select2"  name="param">
        <option value="">انتخاب</option>
        @foreach($data as $key => $item)
            <option value="{{ $value ? $item : $key }}">{{$item}}</option>
        @endforeach
    </select>
</div>
@push('scripts')
    <script>
        $(document).ready(() => {
            $('#{{$id}}').select2()
            $('#{{$id}}').on('change', function (e) {
                var data = $('#{{$id}}').select2("val");
            @this.set('{{$attributes->wire("model")->value}}', data);
            });
        })
    </script>
@endpush
