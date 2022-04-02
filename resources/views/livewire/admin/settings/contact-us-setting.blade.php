<div>
    <x-admin.form-control  title="تنظیمات ارتباط با ما"/>
    <div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body">
            <x-admin.forms.input type="text" id="googleMap" label="شناسه گوگل مپ" wire:model.defer="googleMap"/>
            <x-admin.forms.full-text-editor type="text" id="contactText" label="متن" wire:model.defer="contactText"/>
        </div>
    </div>
</div>
