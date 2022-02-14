<div>
    <x-admin.form-control  title="تنظیمات درباره ما"/>
    <div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body">
            <x-admin.forms.text-area id="aboutUs" label="درباره ما*" wire:model.defer="aboutUs"/>
            <x-admin.forms.lfm-standalone id="aboutUsImages" label="اسلایدر درباره ما*" :file="$aboutUsImages" required="true" wire:model="aboutUsImages"/>
        </div>
    </div>
</div>
