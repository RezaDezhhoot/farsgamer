@props(['options', 'formKey'])

<div class="form-group col-12 d-flex justify-content-between">
    <h6 class="d-inline">لیست مقادیر</h6>
    <button type="button" class="btn btn-success" wire:click="addOption({{$formKey}})">افزودن</button>
</div>

@foreach($options as $optionKey => $option)
    <div class="form-group col-lg-12" style="display: flex;align-items: center">
        <div class="flex-fill" style="width: 100%;">
            <label for="option_value_{{$optionKey}}">عنوان</label>
            <input id="option_value_{{$optionKey}}" type="text" class="form-control"
                   wire:model.defer="formOptions.{{$optionKey}}.name">
        </div>
        <div class="flex-fill ml-1" style="width: 100%;">
            <label for="option_price_{{$optionKey}}">مقدار</label>
            <input id="option_price_{{$optionKey}}" type="text" class="form-control"
                   wire:model.defer="formOptions.{{$optionKey}}.value">
        </div>
        <div class="d-flex align-items-end mx-1" style="margin-top: 24px;">
            <button type="button" class="btn btn-danger" wire:click="deleteOption({{$formKey}},{{$optionKey}})">حذف</button>
        </div>
    </div>
@endforeach
