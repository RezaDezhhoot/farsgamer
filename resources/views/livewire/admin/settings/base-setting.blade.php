<div>
    <x-admin.form-control title="تنطیمات پایه"/>
    <div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body">
            <x-admin.forms.input type="text" id="name" label="نام سایت*" wire:model.defer="name"/>
            <x-admin.forms.input type="text" id="title" label="عنوان سایت*" wire:model.defer="title"/>
            <x-admin.forms.lfm-standalone id="logo" label="لوگو سایت*" :file="$logo" required="true" wire:model="logo"/>
            <x-admin.forms.lfm-standalone id="waterMark" label="تصویر واتر مارک*" :file="$waterMark" required="true" wire:model="waterMark"/>
            <x-admin.forms.dropdown id="status" :data="$data['status']" label="وضعیت سایت*" wire:model.defer="status"/>
            <x-admin.forms.input type="text" id="registerGift" label="هدیه ثبت نام(تومان)" wire:model.defer="registerGift"/>
            <x-admin.forms.lfm-standalone id="logInImage" label="تصویر صفحه ورود*" :file="$logInImage" type="image" required="true" wire:model="logInImage"/>
            <x-admin.forms.text-area label="توضیحات سئو*" id="seoDescription" wire:model.defer="seoDescription" />
            <x-admin.forms.text-area label="کلمات سئو*" help="کلمات را با کاما از هم جدا کنید" id="seoKeyword" wire:model.defer="seoKeyword" />
            <x-admin.forms.input type="text" id="notification" label="اعلان بالای صفحه" wire:model.defer="notification"/>
            <x-admin.forms.input type="text" id="tel" label="تلفن*" wire:model.defer="tel"/>
            <x-admin.forms.input type="email" id="email" label="ایمیل*" wire:model.defer="email"/>
            <x-admin.forms.input type="text" id="address" label="ادرس*" wire:model.defer="address"/>
            <x-admin.forms.input type="number" id="categoryHomeCount" label="تعداد دسته بندی های قابل نمایش صفحه اصلی" wire:model.defer="categoryHomeCount"/>
            <x-admin.button class="primary" content="افزودن لینک ارتباطی" wire:click="addLink()" />
            <x-admin.forms.text-area label="توضیخحات قوانین *"  id="description_law" wire:model.defer="description_law" />
        @foreach($contact as $key => $item)
                <div class="form-group" style="display: flex;align-items: center">
                    <div style="padding: 5px">
                        <input class="form-control" id="{{ $key }}image" type="text" placeholder="تصویر" wire:model.defer="contact.{{$key}}.img">
                    </div>
                    <div style="padding: 5px">
                        <input class="form-control" id="{{ $key }}title" type="text" placeholder="لیتک" wire:model.defer="contact.{{$key}}.link">
                    </div>
                    <div><button class="btn btn-danger" wire:click="delete({{ $key }})">حذف</button></div>
                </div>
            @endforeach
            <x-admin.forms.text-area id="copyRight" label="متن کپی رایت" wire:model.defer="copyRight"/>
            <x-admin.form-section label="موضوعات تیکت">
                <x-admin.button class="primary" content="افزودن موضوع" wire:click="addSubject()" />
                @foreach($subject as $key => $item)
                    <div class="form-group" style="display: flex;align-items: center">
                        <div style="padding: 5px">
                            <input class="form-control" id="{{ $key }}subject" type="text" placeholder="عنوان" wire:model.defer="subject.{{$key}}">
                        </div>
                        <div><button class="btn btn-danger" wire:click="deleteSubject({{ $key }})">حذف</button></div>
                    </div>
                @endforeach
            </x-admin.form-section>
            <x-admin.form-section label="موضوعات تخلف">
                <x-admin.button class="primary" content="افزودن موضوع" wire:click="addOffend()" />
                @foreach($offends as $key => $item)
                    <div class="form-group" style="display: flex;align-items: center">
                        <div style="padding: 5px">
                            <input class="form-control" id="{{ $key }}subject" type="text" placeholder="عنوان" wire:model.defer="offends.{{$key}}">
                        </div>
                        <div><button class="btn btn-danger" wire:click="deleteOffend({{ $key }})">حذف</button></div>
                    </div>
                @endforeach
            </x-admin.form-section>
        </div>
    </div>
</div>
