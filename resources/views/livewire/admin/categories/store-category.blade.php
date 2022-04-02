<div>
    <x-admin.form-control deleteAble="true" deleteContent="حذف دسته" mode="{{$mode}}" title="دسته"/>
    <div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">{{ $header }}</h3>
        </div>
        <x-admin.forms.validation-errors/>
        <div class="card-body">
            <x-admin.forms.input type="text" id="slug" label="نام مستعار*" wire:model.defer="slug"/>
            <x-admin.forms.input type="text" id="title" label="عنوان*" wire:model.defer="title"/>
            <x-admin.forms.lfm-standalone id="logo" label="ایکون*" :file="$logo" type="image" required="true" wire:model="logo"/>
            <x-admin.forms.lfm-standalone id="default_image" label="تصویر پیشفرض برای اگهی های بدون تصویر*" :file="$default_image" type="image" required="true" wire:model="default_image"/>
            <x-admin.forms.lfm-standalone id="slider" label="تصویر پسزمینه*" :file="$slider" type="image" required="true" wire:model="slider"/>
            <x-admin.forms.full-text-editor id="description" label="توضیحات*" wire:model.defer="description"/>
            <x-admin.forms.text-area label="کلمات کلیدی*" help="کلمات را با کاما از هم جدا کنید" wire:model.defer="seo_keywords" id="seo_keywords" />
            <x-admin.forms.text-area label="توضیحات سئو*" wire:model.defer="seo_description" id="seo_description" />
            <x-admin.forms.input type="number" id="pay_time" label="زمان لازم برای پرداخت*" help="بر حسب دقیقه" wire:model.defer="pay_time"/>
            <x-admin.forms.input type="number" id="send_time" label="زمان لازم برای ارسال توسط فروشنده یا خریردار*" help="بر حسب دقیقه" wire:model.defer="send_time"/>
            <x-admin.forms.input type="number" id="receive_time" label="زمان لازم برای دریافت توسط فروشنده یا خریدار(محصولات دیجیتالی)*" help="بر حسب دقیقه" wire:model.defer="receive_time"/>
            <x-admin.forms.input type="number" id="no_receive_time" label="زمان پیگیری در صورت عدم دریافت فروشنده یا خریردار*" help="بر حسب دقیقه" wire:model.defer="no_receive_time"/>
            <x-admin.forms.input type="number" id="commission" label="کارمزد شبکه *" help="بر حسب درصد" wire:model.defer="commission"/>
            <x-admin.forms.input type="number" id="intermediary" label="حق واسطه گری*" help="بر حسب درصد" wire:model.defer="intermediary"/>
            <x-admin.forms.checkbox id="control" label="نیاز به واسط" wire:model.defer="control" />
            <x-admin.forms.dropdown id="status" :data="$data['status']" label="وضعیت*" wire:model.defer="status"/>
            <x-admin.forms.dropdown id="parent_id" :data="$data['category']" label="دسته مادر" wire:model.defer="parent_id"/>
            <x-admin.forms.dropdown id="is_available" :data="$data['is_available']" label="نوع دسته*" wire:model.defer="is_available"/>
            <x-admin.forms.dropdown id="type" :data="$data['type']" label="نوع محصولات*" wire:model.defer="type"/>
            <hr>
            <x-admin.form-section label="پارامتر ها">
                <x-admin.modal-page id="parameter" wire:click="storeParameter()" title="{{ $para }}">
                    <x-admin.forms.validation-errors/>
                    <x-admin.forms.lfm-standalone id="paraLogo" label="ایکون*" :file="$paraLogo" type="image" required="true" wire:model="paraLogo"/>
                    <x-admin.forms.input type="text" id="paraName" label="نام *" wire:model.defer="paraName"/>
                    <x-admin.forms.dropdown id="paraType" :data="['number'=>'عددی','text'=>'رشته ای']" label="نوع ورودی*" wire:model.defer="paraType"/>
                    <x-admin.forms.input type="text" id="paraField" label="مقدار پیشفرض" wire:model.defer="paraField"/>
                    <x-admin.forms.input type="number" id="paraMax" label="حداکثر مقدار" wire:model.defer="paraMax"/>
                    <x-admin.forms.input type="number" id="paraMin" label="حداقل مقدار" wire:model.defer="paraMin"/>
                </x-admin.modal-page>
                <x-admin.button class="primary" content="افزودن پارامتر" wire:click="addParameter('new')" />
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>تصویر</th>
                        <th>عنوان</th>
                        <th>نوع ورودی</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($parameters as $key => $value)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><img src="{{ asset($value['logo']) }}" style="width: 30px;height: 30px" alt=""></td>
                            <td>{{ $value['name'] }}</td>
                            <td>{{ $value['type'] }}</td>
                            <td>
                                <button type="button" wire:click="addParameter({{$key}})" class="btn btn-sm btn-default btn-text-primary btn-hover-primary btn-icon mr-2">
                                    <span class="svg-icon svg-icon-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24"></rect>
                                                <path d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953) "></path>
                                                <path d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                            </g>
                                        </svg>
                                    </span>
                                </button>
                                <x-admin.delete-btn onclick="deletePara({{$key}})" />
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </x-admin.form-section>
            <hr>
            <x-admin.form-section label="روش های ارسال(محصولات فیزیکی)">
                <div class="row" style="display: flex">
                    @foreach($data['transfer'] as $key => $item)
                        <div class="col-lg-2">
                            <x-admin.forms.checkbox label="{{ $item['slug'] }}" value="{{ $item['id'] }}" id="{{ $item['id'] }}transfer"  wire:model.defer="transfer" />
                        </div>
                    @endforeach
                </div>
            </x-admin.form-section>
            <hr>
            <x-admin.form-section label="پلتفرم ها">
                <div class="row" style="display: flex">
                    @foreach($data['platform'] as $key => $item)
                        <div class="col-lg-2">
                            <x-admin.forms.checkbox label="{{ $item['slug'] }}"  value="{{ $item['id'] }}"  id="{{ $item['id'] }}platforms" wire:model.defer="platforms" />
                        </div>
                    @endforeach
                </div>
            </x-admin.form-section>
            <hr>
            <x-admin.form-section label="فیلد های مورد نیاز برای اطلاعات">
                <button class="btn btn-link" wire:click="addForm('select')">لیست</button>
                <button class="btn btn-link" wire:click="addForm('text')">متن</button>
                <button class="btn btn-link" wire:click="addForm('textArea')">باکس متن</button>
                <button class="btn btn-link" wire:click="addForm('customRadio')">گزینه ای</button>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>عنوان</th>
                        <th>عملیات</th>
                    </tr>
                    </thead>
                    <tbody wire:sortable="updateFormPosition()">
                    @forelse($form as $key => $item)
                        <tr wire:sortable.item="{{ $item['name'] }}" wire:key="{{ $item['name'] }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{!! $item['label'] ?? ''!!}</td>
                            <td>
                                <button type="button" wire:click="editForm({{$key}})" class="btn btn-sm btn-default btn-text-primary btn-hover-primary btn-icon mr-2">
                                    <span class="svg-icon svg-icon-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24"></rect>
                                                <path d="M12.2674799,18.2323597 L12.0084872,5.45852451 C12.0004303,5.06114792 12.1504154,4.6768183 12.4255037,4.38993949 L15.0030167,1.70195304 L17.5910752,4.40093695 C17.8599071,4.6812911 18.0095067,5.05499603 18.0083938,5.44341307 L17.9718262,18.2062508 C17.9694575,19.0329966 17.2985816,19.701953 16.4718324,19.701953 L13.7671717,19.701953 C12.9505952,19.701953 12.2840328,19.0487684 12.2674799,18.2323597 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.701953, 10.701953) rotate(-135.000000) translate(-14.701953, -10.701953) "></path>
                                                <path d="M12.9,2 C13.4522847,2 13.9,2.44771525 13.9,3 C13.9,3.55228475 13.4522847,4 12.9,4 L6,4 C4.8954305,4 4,4.8954305 4,6 L4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 L2,6 C2,3.790861 3.790861,2 6,2 L12.9,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                            </g>
                                        </svg>
                                    </span>
                                </button>
                                <x-admin.delete-btn onclick="deleteFormItem({{$key}})" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="3">
                                دیتایی جهت نمایش وجود ندارد
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </x-admin.form-section>
            <x-admin.modal-page id="text" title="text" wire:click="setFormData()">
                <x-admin.forms.validation-errors/>
                <x-admin.forms.input type="text" id="text-name" label="نام*" wire:model.defer="formName" disabled/>
                <x-admin.forms.dropdown id="text-required" label="اجباری*" :data="['0' => 'خیر', '1' => 'بله']" wire:model.defer="formRequired"/>
                <x-admin.forms.dropdown id="text-width" label="عرض*" :data="['6' => '50 درصد', '12' => '100 درصد']"  wire:model.defer="formWidth"/>
                <x-admin.forms.dropdown id="text-for" label="مخاطب*" :data="['seller'=>'فروشنده','customer'=>'خریدار']"  wire:model.defer="formFor"/>
                <x-admin.forms.dropdown id="text-status" label="روال*" :data="['normal'=>'عادی','return'=>'مرجوعی']"  wire:model.defer="formStatus"/>
                <x-admin.forms.full-text-editor id="text" label="برچسب فیلد*" wire:model.defer="formLabel"/>
                <x-admin.forms.input type="text" id="text-placeholder" label="متن پیشفرض" wire:model.defer="formPlaceholder"/>
                <x-admin.forms.input type="text" id="text-value" label="مقدار" wire:model.defer="formValue"/>
            </x-admin.modal-page>
            <x-admin.modal-page id="select" title="select" wire:click="setFormData()">
                <x-admin.forms.validation-errors/>
                <x-admin.forms.input type="text" id="select-name" label="نام*" wire:model.defer="formName" disabled/>
                <x-admin.forms.dropdown id="select-required" label="اجباری*" :data="['0' => 'خیر', '1' => 'بله']" wire:model.defer="formRequired"/>
                <x-admin.forms.dropdown id="select-width" label="عرض*" :data="['6' => '50 درصد', '12' => '100 درصد']"  wire:model.defer="formWidth"/>
                <x-admin.forms.dropdown id="select-for" label="مخاطب*" :data="$data['for']"  wire:model.defer="formFor"/>
                <x-admin.forms.dropdown id="select-status" label="روال*" :data="['normal'=>'عادی','return'=>'مرجوعی']"  wire:model.defer="formStatus"/>
                <x-admin.forms.full-text-editor id="select" label="برچسب فیلد*" wire:model.defer="formLabel"/>
                <x-admin.forms.input type="text" id="select-value" label="مقدار" wire:model.defer="formValue"/>
                <x-admin.forms.form-options :options="$formOptions ?? []" :formKey="$formKey"/>
            </x-admin.modal-page>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function deleteItem(id) {
            Swal.fire({
                title: 'حذف دسته بندی!',
                text: 'آیا از حذف این دسته بندی اطمینان دارید؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'خیر',
                confirmButtonText: 'بله'
            }).then((result) => {
                if (result.value) {
                    if (result.isConfirmed) {
                        Swal.fire(
                            'موفیت امیز!',
                            'دسته بندی مورد نظر با موفقیت حذف شد',
                        )
                    }
                @this.call('deleteItem', id)
                }
            })
        }
        function deletePara(id) {
            Swal.fire({
                title: 'حذف پارامتر !',
                text: 'آیا از حذف این پارامتر  اطمینان دارید؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'خیر',
                confirmButtonText: 'بله'
            }).then((result) => {
                if (result.value) {
                    if (result.isConfirmed) {
                        Swal.fire(
                            'موفیت امیز!',
                            'پارامتر  مورد نظر با موفقیت حذف شد',
                        )
                    }
                @this.call('deleteParameter', id)
                }
            })
        }
        function deleteFormItem(id) {
            Swal.fire({
                title: 'حذف فرم!',
                text: 'آیا از حذف فرم اطمینان دارید؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'خیر',
                confirmButtonText: 'بله'
            }).then((result) => {
                if (result.value) {
                @this.call('deleteForm', id)
                }
            })
        }
    </script>
@endpush
