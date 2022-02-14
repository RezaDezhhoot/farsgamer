@props(['id', 'label'])
<div class="form-group" wire:ignore>
    <div>
        <label for="{{$id}}">{{$label}} </label>
        <textarea {{ $attributes->wire('model') }} id="{{$id}}" class="resizable_textarea form-control"
                  x-data="{text: @entangle($attributes->wire('model')) }"
                  x-init="CKEDITOR.replace('{{$id}}', {
                            language: 'fa',
                            filebrowserImageBrowseUrl: '/filemanager?type=Files',
                            filebrowserImageUploadUrl: '/filemanager/upload?type=Images&_token=',
                            filebrowserBrowseUrl: '/filemanager?type=Files',
                            filebrowserUploadUrl: '/filemanager/upload?type=Files&_token='
                        });
                        CKEDITOR.instances.{{$id}}.on('change', function () {
                            $dispatch('input', CKEDITOR.instances.{{$id}}.getData())
                        });"
                  x-text="CKEDITOR.instances.{{$id}}.setData(this.text); return this.text">
            </textarea>
    </div>
    <script src="https://cdn.ckeditor.com/4.13.0/full/ckeditor.js"></script>
</div>

