@props(['title','id'])
<div  class="modal fade" data-focus="false"  id="{{$id}}Modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="Modal" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {{ $title  }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-1"></div>
                    <div class="col-10">
                        {{ $slot }}
                    </div>
                    <div class="col-1"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">انصراف</button>
                <button id="send" type="button" class="btn btn-success" {{ $attributes }}>ثبت</button>
            </div>
        </div>
    </div>
</div>
