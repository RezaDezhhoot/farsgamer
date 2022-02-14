<div>
    <x-admin.chat-panel :chats="$chats" id="kt_chat_modal"  />
    <x-admin.form-control chatAble="{{true}}" title="{{ $header }}"/>
    <div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body">
            <x-admin.forms.input type="text" id="first_name" label="نام*" wire:model.defer="first_name"/>
            <x-admin.forms.input type="text" id="last_name" label="نام خانوادگی*" wire:model.defer="last_name"/>
            <x-admin.forms.input type="text" id="user_name" label="نام کاربری*" wire:model.defer="user_name"/>
            <x-admin.forms.input type="text" id="phone" label="شماره همراه*" wire:model.defer="phone"/>
            <x-admin.forms.input type="email" id="email" label="ایمیل*" wire:model.defer="email"/>
            <x-admin.forms.text-area  id="description" label="بایوگرافی" wire:model.defer="description"/>
            <x-admin.forms.input type="password" id="pass_word" help="حداقل {{ \App\Models\Setting::getSingleRow('password_length')}} حرف شامل اعداد و حروف" label="گدرواژه" wire:model.defer="pass_word"/>
            <div class="form-group">
                <label class="form-label" for="file">تصویر پروفایل</label>
                <div x-data="{ isUploading: false, progress: 0 }"
                     x-on:livewire-upload-start="isUploading = true"
                     x-on:livewire-upload-finish="isUploading = false"
                     x-on:livewire-upload-error="isUploading = false"
                     x-on:livewire-upload-progress="progress = $event.detail.progress">
                    <input type="file" id="file" wire:model="file" aria-label="file" />

                    <div class="mt-2" x-show="isUploading">
                        در حال اپلود تصویر...
                        <progress max="100" x-bind:value="progress"></progress>
                    </div>
                    <br>
                    <small class="text-info">حداقل حجم مجاز : {{\App\Models\Setting::getSingleRow('max_profile_image_size')}} کیلوبایت</small>
                </div>
                <br>
                @if($user->profile_image)
                    <img style="max-width: 150px;border-radius: 5px" src="{{asset($user->profile_image)}}" alt="">
                @endif
            </div>
            <x-admin.form-section label="نقش های من">
                <ul>
                    @foreach($user->roles as $item)
                        <il>
                            <h5>{{$item->name}}</h5>
                            <hr>
                        </il>
                    @endforeach
                </ul>
            </x-admin.form-section>
        </div>
    </div>
</div>
