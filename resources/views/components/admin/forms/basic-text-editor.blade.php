@props(['id', 'label'])
<div style="padding: 5px">
    <div wire:ignore>
        <label for="{{$id}}">{{$label}} </label>
        <textarea {{ $attributes->wire('model') }} id="{{$id}}" x-data="{text: @entangle($attributes->wire('model')) }"
                  x-init="CKEDITOR.replace('{{$id}}', {
                            language: 'fa',
                        });
                        CKEDITOR.instances.{{$id}}.on('change', function () {
                            $dispatch('input', CKEDITOR.instances.{{$id}}.getData())
                        });"
                  x-text="CKEDITOR.instances.{{$id}}.setData(this.text); return this.text"></textarea>
    </div>
</div>
<script src="https://cdn.ckeditor.com/4.13.0/basic/ckeditor.js"></script>
