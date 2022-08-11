@props(['id', 'label', 'required' => false, 'help', 'file' , 'type' => 'file', 'disable'=>false])
<div>
    <div class="form-group col-12">
        <label for="{{$id}}">{{$label}}</label>
        @if(!$disable)
            <div class="input-group">
                <input type="text" {{ $attributes->wire('model') }} id="{{$id}}" {!! $attributes->merge(['class'=>
            'form-control']) !!} name="image"
                       aria-label="Image" aria-describedby="button-image"
                       x-data
                       x-init="$('#{{$id}}').on('change', function () {alert(2); $dispatch('input', $(this).val()) })"
                >
                <div class="input-group-append">
                    <button {{$disable ? 'disabled' : '' }} class="btn btn-outline-secondary" type="button"
                            id="button-{{$id}}">انتخاب</button>
                </div>
            </div>
        @endif
    </div>
    @if(gettype($file) == 'string')
        <div class="form-group col-12">
            @foreach(explode(',', $file) as $key => $item)
                <img src="{{asset($item)}}" alt="{{$id}}" width="120px" height="100px" class="mr-1 mb-1 imglist"
                     style="border-radius: 5px" />
            @endforeach
        </div>
    @endif


</div>

@push('scripts')
    <script>
        var id , input , input_id;
        document.addEventListener("DOMContentLoaded", function() {

            document.getElementById('button-{{$id}}').addEventListener('click', (event) => {
                event.preventDefault();
                id = event.target.id;
                input_id = id.replace("button-", '');
                input = document.getElementById(input_id);
                window.open('/file-manager/fm-button', 'fm', 'width=1400,height=800');
            });
        });


        // set file link

        function fmSetLink($url) {
            input.value = $url;
        @this.set(input_id, $url);
        }
    </script>
@endpush
