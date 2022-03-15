<div  wire:init="emitEvent()">
    <div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-1">
                <!--begin::Page Heading-->
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <!--begin::Page Title-->
                    <h5 class="text-dark font-weight-bold my-1 mr-5">داشبورد</h5>
                    <!--end::Page Title-->
                </div>
                <!--end::Page Heading-->
            </div>
            <!--end::Info-->
            <!--begin::Toolbar-->
            <div class="d-flex align-items-center">
            <!--begin::Actions-->
               <div class="d-flex align-center justify-content-between">
                   <x-admin.forms.date-picker id="date" label="از "   wire:model.defer="from_date"/>
               </div>
                <div>
                    <x-admin.forms.date-picker id="date2" label="تا"  wire:model.defer="to_date"/>
                </div>
                <div>
                    <button class="btn btn-light-primary font-weight-bolder btn-sm" wire:click="confirmFilter">اعمال تاریخ</button>
                </div>
            <!--end::Actions-->
            </div>
            <!--end::Toolbar-->
        </div>
    </div>
    <div class="card card-custom">
        <div class="card-body">
            <div class="row">
                <div class="col-xl-4">
                    <!--begin::Stats Widget 25-->
                    <div class="card card-custom bg-light-success card-stretch gutter-b">
                        <!--begin::Body-->
                        <div class="card-body">
                            <span class="svg-icon svg-icon-primary svg-icon-4x"><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\legacy\metronic\theme\html\demo1\dist/../src/media/svg/icons\Cooking\Cutting board.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"/>
                                    <path d="M8.37867966,15.1213203 C9.35499039,16.0976311 9.35499039,17.6805435 8.37867966,18.6568542 L6.25735931,20.7781746 C5.28104858,21.7544853 3.69813614,21.7544853 2.72182541,20.7781746 C1.74551468,19.8018639 1.74551468,18.2189514 2.72182541,17.2426407 L4.84314575,15.1213203 C5.81945648,14.1450096 7.40236893,14.1450096 8.37867966,15.1213203 Z M3.81784105,19.7528699 C4.30599642,20.2410253 5.09745264,20.2410253 5.58560801,19.7528699 C6.07376337,19.2647145 6.07376337,18.4732583 5.58560801,17.9851029 C5.09745264,17.4969476 4.30599642,17.4969476 3.81784105,17.9851029 C3.32968569,18.4732583 3.32968569,19.2647145 3.81784105,19.7528699 Z" fill="#000000" opacity="0.3"/>
                                    <path d="M14.3890873,1.33273811 L22.1672619,9.1109127 C22.9483105,9.89196129 22.9483105,11.1582912 22.1672619,11.9393398 L12.9748737,21.131728 C12.1938252,21.9127766 10.9274952,21.9127766 10.1464466,21.131728 L2.36827202,13.3535534 C1.58722343,12.5725048 1.58722343,11.3061748 2.36827202,10.5251263 L11.5606602,1.33273811 C12.3417088,0.551689527 13.6080387,0.551689527 14.3890873,1.33273811 Z" fill="#000000"/>
                                </g>
                            </svg><!--end::Svg Icon--></span>
                            <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 mt-6 d-block">
                                {{ $box['orders'] }}عدد
                            </span>
                            <span class="font-weight-bold text-dark font-size-lg">اگهی های ثبت شده</span>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 25-->
                </div>
                <div class="col-xl-4">
                    <!--begin::Stats Widget 27-->
                    <div class="card card-custom bg-light-info card-stretch gutter-b">
                        <!--begin::Body-->
                        <div class="card-body">
                            <span class="svg-icon svg-icon-primary svg-icon-4x"><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\legacy\metronic\theme\html\demo1\dist/../src/media/svg/icons\Navigation\Exchange.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"/>
                                    <rect fill="#000000" opacity="0.3" transform="translate(13.000000, 6.000000) rotate(-450.000000) translate(-13.000000, -6.000000) " x="12" y="8.8817842e-16" width="2" height="12" rx="1"/>
                                    <path d="M9.79289322,3.79289322 C10.1834175,3.40236893 10.8165825,3.40236893 11.2071068,3.79289322 C11.5976311,4.18341751 11.5976311,4.81658249 11.2071068,5.20710678 L8.20710678,8.20710678 C7.81658249,8.59763107 7.18341751,8.59763107 6.79289322,8.20710678 L3.79289322,5.20710678 C3.40236893,4.81658249 3.40236893,4.18341751 3.79289322,3.79289322 C4.18341751,3.40236893 4.81658249,3.40236893 5.20710678,3.79289322 L7.5,6.08578644 L9.79289322,3.79289322 Z" fill="#000000" fill-rule="nonzero" transform="translate(7.500000, 6.000000) rotate(-270.000000) translate(-7.500000, -6.000000) "/>
                                    <rect fill="#000000" opacity="0.3" transform="translate(11.000000, 18.000000) scale(1, -1) rotate(90.000000) translate(-11.000000, -18.000000) " x="10" y="12" width="2" height="12" rx="1"/>
                                    <path d="M18.7928932,15.7928932 C19.1834175,15.4023689 19.8165825,15.4023689 20.2071068,15.7928932 C20.5976311,16.1834175 20.5976311,16.8165825 20.2071068,17.2071068 L17.2071068,20.2071068 C16.8165825,20.5976311 16.1834175,20.5976311 15.7928932,20.2071068 L12.7928932,17.2071068 C12.4023689,16.8165825 12.4023689,16.1834175 12.7928932,15.7928932 C13.1834175,15.4023689 13.8165825,15.4023689 14.2071068,15.7928932 L16.5,18.0857864 L18.7928932,15.7928932 Z" fill="#000000" fill-rule="nonzero" transform="translate(16.500000, 18.000000) scale(1, -1) rotate(270.000000) translate(-16.500000, -18.000000) "/>
                                </g>
                            </svg><!--end::Svg Icon--></span>
                            <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 mt-6 d-block">
                                {{$box['order-transactions']}}عدد
                            </span>
                            <span class="font-weight-bold text-dark font-size-lg">معاملات</span>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 27-->
                </div>
                <div class="col-xl-4">
                    <!--begin::Stats Widget 26-->
                    <div class="card card-custom bg-light-danger card-stretch gutter-b">
                        <!--begin::ody-->
                        <div class="card-body">
												<span class="svg-icon svg-icon-4x svg-icon-danger">
													<!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Group.svg-->
													<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
														<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
															<polygon points="0 0 24 0 24 24 0 24"></polygon>
															<path d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
															<path d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
														</g>
													</svg>
                                                    <!--end::Svg Icon-->
												</span>
                            <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 mt-6 d-block">
                                {{$box['users'] }}عدد
                            </span>
                            <span class="font-weight-bold text-dark font-size-lg">کاربر جدید</span>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 26-->
                </div>
            </div>
            <div class="row">
                <div class="col-xl-4">
                    <!--begin::Stats Widget 25-->
                    <div class="card card-custom bg-light-success card-stretch gutter-b">
                        <!--begin::Body-->
                        <div class="card-body">
                            <span class="svg-icon svg-icon-primary svg-icon-4x"><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\legacy\metronic\theme\html\demo1\dist/../src/media/svg/icons\Shopping\Money.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"/>
                                    <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z" fill="#000000" opacity="0.3" transform="translate(11.500000, 12.000000) rotate(-345.000000) translate(-11.500000, -12.000000) "/>
                                    <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z M11.5,14 C12.6045695,14 13.5,13.1045695 13.5,12 C13.5,10.8954305 12.6045695,10 11.5,10 C10.3954305,10 9.5,10.8954305 9.5,12 C9.5,13.1045695 10.3954305,14 11.5,14 Z" fill="#000000"/>
                                </g>
                            </svg><!--end::Svg Icon--></span>
                            <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 mt-6 d-block">
                                {{ number_format($box['payments']) }}تومان
                            </span>
                            <span class="font-weight-bold text-dark font-size-lg">مبالغ پرداخت شده</span>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 25-->
                </div>
                <div class="col-xl-4">
                    <!--begin::Stats Widget 26-->
                    <div class="card card-custom bg-light-danger card-stretch gutter-b">
                        <!--begin::ody-->
                        <div class="card-body">
                            <span class="svg-icon svg-icon-primary svg-icon-4x"><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\legacy\metronic\theme\html\demo1\dist/../src/media/svg/icons\Shopping\Money.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"/>
                                    <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z" fill="#000000" opacity="0.3" transform="translate(11.500000, 12.000000) rotate(-345.000000) translate(-11.500000, -12.000000) "/>
                                    <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z M11.5,14 C12.6045695,14 13.5,13.1045695 13.5,12 C13.5,10.8954305 12.6045695,10 11.5,10 C10.3954305,10 9.5,10.8954305 9.5,12 C9.5,13.1045695 10.3954305,14 11.5,14 Z" fill="#000000"/>
                                </g>
                            </svg><!--end::Svg Icon--></span>
                            <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 mt-6 d-block">
                                {{ number_format($box['requests']) }}تومان
                            </span>
                            <span class="font-weight-bold text-dark font-size-lg">مبالغ درخواست شده</span>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 26-->
                </div>
                <div class="col-xl-4">
                    <!--begin::Stats Widget 27-->
                    <div class="card card-custom bg-light-info card-stretch gutter-b">
                        <!--begin::Body-->
                        <div class="card-body">
                            <span class="svg-icon svg-icon-primary svg-icon-4x"><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\legacy\metronic\theme\html\demo1\dist/../src/media/svg/icons\Shopping\Money.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"/>
                                    <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z" fill="#000000" opacity="0.3" transform="translate(11.500000, 12.000000) rotate(-345.000000) translate(-11.500000, -12.000000) "/>
                                    <path d="M2,6 L21,6 C21.5522847,6 22,6.44771525 22,7 L22,17 C22,17.5522847 21.5522847,18 21,18 L2,18 C1.44771525,18 1,17.5522847 1,17 L1,7 C1,6.44771525 1.44771525,6 2,6 Z M11.5,16 C13.709139,16 15.5,14.209139 15.5,12 C15.5,9.790861 13.709139,8 11.5,8 C9.290861,8 7.5,9.790861 7.5,12 C7.5,14.209139 9.290861,16 11.5,16 Z M11.5,14 C12.6045695,14 13.5,13.1045695 13.5,12 C13.5,10.8954305 12.6045695,10 11.5,10 C10.3954305,10 9.5,10.8954305 9.5,12 C9.5,13.1045695 10.3954305,14 11.5,14 Z" fill="#000000"/>
                                </g>
                            </svg><!--end::Svg Icon--></span>
                            <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 mt-6 d-block">
                                {{ number_format($box['settlements']) }}تومان
                            </span>
                            <span class="font-weight-bold text-dark font-size-lg">مبالغ تسویه شده</span>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 27-->
                </div>
            </div>
            <div class="row" wire:ignore>
                <div class="col-xl-12">
                    <!--begin::Charts Widget 4-->
                    <div class="card card-custom card-stretch gutter-b">
                        <!--begin::Header-->
                        <div class="card-header h-auto border-0">
                            <div class="card-title py-5">
                                <h3 class="card-label">
                                    <span class="d-block text-dark font-weight-bolder">گردش مالی</span>
                                </h3>
                            </div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body">
                            <div id="kt_charts_widget_4_chart2"></div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Charts Widget 4-->
                </div>
            </div>
            <div class="row">
                <div class="col-xl-6">
                    <!--begin::List Widget 10-->
                    <div class="card card-custom card-stretch gutter-b">
                        <!--begin::Header-->
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bolder text-dark">محبوب ترین دسته بندی ها</h3>
                            <div class="card-toolbar">
                                <div class="dropdown dropdown-inline">
                                </div>
                            </div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-0">
                            <!--begin::Item-->
                            @foreach($list['categories'] as $item)
                                <div class="mb-6">
                                    <!--begin::Content-->
                                    <div class="d-flex align-items-center flex-grow-1">
                                        <!--begin::Checkbox-->
                                        <label class="checkbox checkbox-lg checkbox-lg flex-shrink-0 mr-4">
                                            <input type="checkbox" value="1">
                                            <span style="width: 43px;height: 43px">
                                                <img style="max-width: 100%;height: 100%;border-radius: 8px" src="{{asset($item->logo)}}" alt="">
                                            </span>
                                        </label>
                                        <!--end::Checkbox-->
                                        <!--begin::Section-->
                                        <div class="d-flex flex-wrap align-items-center justify-content-between w-100">
                                            <!--begin::Info-->
                                            <div class="d-flex flex-column align-items-cente py-2 w-75">
                                                <!--begin::Title-->
                                                <a href="{{route('admin.store.category',['edit',$item->id])}}" class="text-dark-75 font-weight-bold text-hover-primary font-size-lg mb-1">
                                                    {{ $item->title }}
                                                </a>
                                                <!--end::Title-->
                                                <!--begin::Data-->
                                                <span class="text-muted font-weight-bold">
                                                    {{ $item->orders->count() }} اگهی ثبت شده
                                                </span>
                                                <!--end::Data-->
                                            </div>
                                            <!--end::Info-->
                                            <!--begin::Label-->
                                            <span class="label label-lg label-light-primary label-inline font-weight-bold py-4">
                                                {{ $item->typeLabel }}
                                            </span>
                                            <!--end::Label-->
                                        </div>
                                        <!--end::Section-->
                                    </div>
                                    <!--end::Content-->
                                </div>
                            @endforeach
                            <!--end::Item-->
                        </div>
                        <!--end: Card Body-->
                    </div>
                    <!--end: Card-->
                    <!--end: List Widget 10-->
                </div>
                <div class="col-xl-6">
                    <!--begin::List Widget 10-->
                    <div class="card card-custom card-stretch gutter-b">
                        <!--begin::Header-->
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bolder text-dark">محبوب ترین پلتفرم ها</h3>
                            <div class="card-toolbar">
                                <div class="dropdown dropdown-inline">
                                </div>
                            </div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-0">
                            <!--begin::Item-->
                            @foreach($list['platforms'] as $item)
                                <div class="mb-6">
                                    <!--begin::Content-->
                                    <div class="d-flex align-items-center flex-grow-1">
                                        <!--begin::Checkbox-->
                                        <label class="checkbox checkbox-lg checkbox-lg flex-shrink-0 mr-4">
                                            <input type="checkbox" value="1">
                                            <span style="width: 43px;height: 43px">
                                                <img style="max-width: 100%;height: 100%;border-radius: 8px" src="{{asset($item->logo)}}" alt="">
                                            </span>
                                        </label>
                                        <!--end::Checkbox-->
                                        <!--begin::Section-->
                                        <div class="d-flex flex-wrap align-items-center justify-content-between w-100">
                                            <!--begin::Info-->
                                            <div class="d-flex flex-column align-items-cente py-2 w-75">
                                                <!--begin::Title-->
                                                <a href="{{route('admin.store.platform',['edit',$item->id])}}" class="text-dark-75 font-weight-bold text-hover-primary font-size-lg mb-1">
                                                    {{ $item->slug }}
                                                </a>
                                                <!--end::Title-->
                                                <!--begin::Data-->
                                                <span class="text-muted font-weight-bold">
                                                    {{ $item->orders->count() }}  اگهی تحت این پلتفرم
                                                </span>
                                                <!--end::Data-->
                                            </div>
                                            <!--end::Info-->
                                            <!--begin::Label-->
                                            <span class="label label-lg label-light-primary label-inline font-weight-bold py-4">
                                               <a href="{{route('admin.order')}}">مشاهده اگهی ها</a>
                                            </span>
                                            <!--end::Label-->
                                        </div>
                                        <!--end::Section-->
                                    </div>
                                    <!--end::Content-->
                                </div>
                        @endforeach
                        <!--end::Item-->
                        </div>
                        <!--end: Card Body-->
                    </div>
                    <!--end: Card-->
                    <!--end: List Widget 10-->
                </div>
            </div>
            <div class="row">
                <div class="col-xl-6">
                    <!--begin::List Widget 10-->
                    <div class="card card-custom card-stretch gutter-b">
                        <!--begin::Header-->
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bolder text-dark">اخرین کامنت ها</h3>
                            <div class="card-toolbar">
                                <div class="dropdown dropdown-inline">
                                </div>
                            </div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-0">
                            <!--begin::Item-->
                            @foreach($list['comments'] as $item)
                                <div class="mb-6">
                                    <!--begin::Content-->
                                    <div class="d-flex align-items-center flex-grow-1">
                                        <!--begin::Checkbox-->
                                        <label class="checkbox checkbox-lg checkbox-lg flex-shrink-0 mr-4">
                                            <input type="checkbox" value="1">
                                            <span style="width: 43px;height: 43px">
                                                <img style="max-width: 100%;height: 100%;border-radius: 8px" src="{{asset($item->user->profile_image)}}" alt="">
                                            </span>
                                        </label>
                                        <!--end::Checkbox-->
                                        <!--begin::Section-->
                                        <div class="d-flex flex-wrap align-items-center justify-content-between w-100">
                                            <!--begin::Info-->
                                            <div class="d-flex flex-column align-items-cente py-2 w-75">
                                                <!--begin::Title-->
                                                <a href="{{route('admin.store.comment',['edit',$item->id])}}" class="text-dark-75 font-weight-bold text-hover-primary font-size-lg mb-1">
                                                    {!! $item->content !!}
                                                </a>
                                                <!--end::Title-->
                                                <!--begin::Data-->
                                                <span class="text-muted font-weight-bold">
                                                    {{ $item->created_at ? $item->created_at->diffForHumans() : ''  }}
                                                </span>
                                                <!--end::Data-->
                                            </div>
                                            <!--end::Info-->
                                            <!--begin::Label-->
                                            <span class="label label-lg label-light-primary label-inline font-weight-bold py-4">
                                                {{ $item->commentable->title ??  $item->commentable->fullName }}
                                            </span>
                                            <span class="label label-lg label-light-primary label-inline font-weight-bold py-4">
                                                {{ $item->statusLabel }}
                                            </span>
                                            <!--end::Label-->
                                        </div>
                                        <!--end::Section-->
                                    </div>
                                    <!--end::Content-->
                                </div>
                        @endforeach
                        <!--end::Item-->
                        </div>
                        <!--end: Card Body-->
                    </div>
                    <!--end: Card-->
                    <!--end: List Widget 10-->
                </div>
                <div class="col-xl-6">
                    <!--begin::List Widget 10-->
                    <div class="card card-custom card-stretch gutter-b">
                        <!--begin::Header-->
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bolder text-dark">اخرین تیکت ها</h3>
                            <div class="card-toolbar">
                                <div class="dropdown dropdown-inline">
                                </div>
                            </div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-0">
                            <!--begin::Item-->
                            @foreach($list['tickets'] as $item)
                                <div class="mb-6">
                                    <!--begin::Content-->
                                    <div class="d-flex align-items-center flex-grow-1">
                                        <!--begin::Checkbox-->
                                        <label class="checkbox checkbox-lg checkbox-lg flex-shrink-0 mr-4">
                                            <input type="checkbox" value="1">
                                            <span style="width: 43px;height: 43px">
                                                <img style="max-width: 100%;height: 100%;border-radius: 8px" src="{{asset($item->user->profile_image)}}" alt="">
                                            </span>
                                        </label>
                                        <!--end::Checkbox-->
                                        <!--begin::Section-->
                                        <div class="d-flex flex-wrap align-items-center justify-content-between w-100">
                                            <!--begin::Info-->
                                            <div class="d-flex flex-column align-items-cente py-2 w-75">
                                                <!--begin::Title-->
                                                <a href="{{route('admin.store.ticket',['edit',$item->id])}}" class="text-dark-75 font-weight-bold text-hover-primary font-size-lg mb-1">
                                                    {{ $item->subject }}
                                                </a>
                                                <!--end::Title-->
                                                <!--begin::Data-->
                                                <span class="text-muted font-weight-bold">
                                                    {!! $item->content !!}
                                                </span>
                                                <!--end::Data-->
                                            </div>
                                            <!--end::Info-->
                                            <!--begin::Label-->
                                            <span class="label label-lg label-light-primary label-inline font-weight-bold py-4">
                                                {{ $item->date }}
                                            </span>
                                            <!--end::Label-->
                                        </div>
                                        <!--end::Section-->
                                    </div>
                                    <!--end::Content-->
                                </div>
                        @endforeach
                        <!--end::Item-->
                        </div>
                        <!--end: Card Body-->
                    </div>
                    <!--end: Card-->
                    <!--end: List Widget 10-->
                </div>
            </div>
        </div>
    </div>
</div>
