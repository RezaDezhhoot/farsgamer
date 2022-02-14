<div>
    <x-admin.form-control title="امنیت"/>
    <div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body">
            <x-admin.form-section label=" پایه">
                <x-admin.forms.input type="text" id="google" label="شناسه گوگل*" wire:model.defer="google"/>
                <x-admin.forms.input type="number" id="password_length" label="حداقل طول پسورد*" wire:model.defer="password_length"/>
                <x-admin.forms.input type="number" id="dos_count" label="حداکثر امکان برای درخواست های پیوسته سمت سرور*" wire:model.defer="dos_count"/>
                <x-admin.forms.input type="number" help="بر حسب کیلوبایت" id="max_profile_image_size" label="حداکثر حجم تصاویر پروفایل*" wire:model.defer="max_profile_image_size"/>
                <x-admin.forms.input type="number" help="بر حسب کیلوبایت" id="max_order_image_size" label="حداکثر حجم تصاویر اگهی ها*" wire:model.defer="max_order_image_size"/>
                <x-admin.forms.input type="text" help="فرمت هارا با کاما از هم جدا کنید" id="valid_order_images" label="فرمت های مجاز تصاویر اگهی ها*" wire:model.defer="valid_order_images"/>
                <x-admin.forms.input type="text" help="فرمت هارا با کاما از هم جدا کنید" id="valid_ticket_files" label="فرمت های مجاز فایل های تیکت*" wire:model.defer="valid_ticket_files"/>
                <x-admin.forms.input type="number" id="ticket_per_day" label="حداکثر دفعات ارسال تیکت در روز*" wire:model.defer="ticket_per_day"/>
                <x-admin.forms.input type="number" id="min_price_to_request" label="حداقل موجودی لازم برای برداشت(تومان)*" wire:model.defer="min_price_to_request"/>
                <x-admin.forms.text-area id="auth_note" label="متن توضیح برای احراز هویت" wire:model.defer="auth_note"/>
                <x-admin.forms.lfm-standalone id="auth_image_pattern" label="تصویر نمونه برای احراز هویت*" :file="$auth_image_pattern" type="image" required="true" wire:model="auth_image_pattern"/>
            </x-admin.form-section>
            <x-admin.form-section label=" تحریم ها">
                @foreach($data['boycott'] as $key => $value)
                    <div class="row">
                        <x-admin.form-section label="{{ $key }}">
                            <div class="row">
                                @foreach($value as $c => $item)
                                    <div class="col-3">
                                        <x-admin.forms.checkbox id="{{ $item }}" label="{{ $item }}" wire:model.defer="boycott.{{$c}}" />
                                    </div>
                                @endforeach
                            </div>
                        </x-admin.form-section>
                    </div>
                    <hr>
                @endforeach
            </x-admin.form-section>
        </div>
    </div>
</div>
