<div>
    <div class="all d-flex flex-lg-row flex-column">
        <!-- SideBar section -->
        <div class="sideBar d-none d-md-flex flex-column" wire:ignore>
            <!-- SideBar top -->
            <div
                class="sideBar-top d-flex flex-column justify-content-start align-items-center mt-5"
            >

                <img
                    src="{{ auth()->check() ? asset(auth()->user()->profile_image) : asset('site/assets/img/ProfilePhoto.png')}}"
                    alt="ProfilePhoto"
                    class="ProfilePhoto"
                />
                <!-- SideBar-top text icon -->

                <div class="sideBar-top-text d-flex flex-row mt-2">
                    @if(!auth()->check())
                        <span class="material-icons color-gray"> person_outline </span>
                        <a class="sideBar-top-text-a color-gray" href="{{ route('auth',['mode' => 'login']) }}">ورود</a>
                        <a class="sideBar-top-text-a color-gray" href="{{ route('auth',['mode' => 'register']) }}">/ثبت نام</a>
                    @else
                        <a class="sideBar-top-text-a color-gray" href="{{ route('user.dashboard') }}">{{ auth()->user()->user_name }}</a>
                    @endif
                </div>
                <!-- SideBar btn section -->
                <div class="sideBar-bts d-flex flex-row justify-content-center">
                    <button wire:loading.attr="disabled"
                        class="Cbtn d-flex flex-row align-items-center justify-content-center mt-2" wire:click="newOrder()"
                    >
                        <span class="material-icons">add</span>
                        ثبت آگهی
                    </button>
                </div>
                <!-- SideBar Categories section -->
                <div class="sideBar-Categories d-flex flex-column w-75">
                    <div
                        class="d-flex flex-row justify-content-center align-items-center Categories-header mt-3 mb-4"
                    >
                        <span class="material-icons">grid_view</span>
                        <p class="p-0 m-0">دسته بندی محصولات</p>
                    </div>

                    @foreach($original_categories as $item)
                        <div
                            class="d-flex flex-row justify-content-start align-items-center mb-4 cursPointer"
                            data-bs-toggle="collapse"
                            data-bs-target="#{{$item['slug']}}Collaps"
                            aria-expanded="false"
                            aria-controls="{{$item['slug']}}Collaps"
                        >
                            <img src="{{asset($item['logo'])}}" alt="steam logo" />
                            <p class="p-0 side-item">{{ $item['title'] }}</p>
                        </div>
                        <div class="collapse" id="{{$item['slug']}}Collaps">
                            <ul class="filter-name">
                                @foreach($item['sub_categories'] as $sub_categories)
                                    <li class="cursPointer {{ in_array($sub_categories['id'],$category) ? 'active-li' : '' }}" onclick="ActiveDeActive(this);setCategory('{{$sub_categories['id']}}')">{{$sub_categories['title']}}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach

                    <!-- test for Custom checkBox -->
                    <div class="filteringCollaps d-flex flex-column">
                        <div
                            class="filterItem d-flex flex-row align-items-center pt-2 pb-2 cursPointer"
                            data-bs-toggle="collapse"
                            data-bs-target="#filterItem1"
                            aria-expanded="false"
                            aria-controls="filterItem1"
                        >
                            <span class="material-icons">expand_more</span>
                            <p class="m-0 p-0">پلتفرم ها</p>
                        </div>
                        <div class="collapse" id="filterItem1">
                            @foreach($platforms as $key => $item)
                                <label class="d-flex flex-row align-items-center cursPointer filterItem-items">
                                    <label class="CustomeCheckBox">
                                        <input type="radio" name="platform" value="{{$item}}" wire:model="platform" />
                                        <span class="checkmark"></span>
                                    </label>
                                    <p class="p-0 m-0">{{ $item }}</p>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="filteringCollaps d-flex flex-row flex-wrap">
                        <a href="#">قوانین</a>
                        <a href="#">ارتباط با ما</a>
                        <a href="#">سوالات متداول</a>
                        <a href="#">درباره ما</a>
                    </div>

                    <div class="socialMedia d-flex flex-row flex-wrap justify-content-around pt-3">
                        @foreach($contact as $item)
                            <a href="{{$item['link']}}">
                                <img
                                    class="cursPointer"
                                    src="{{asset($item['img'])}}"
                                    alt=""/>
                            </a>
                        @endforeach
                    </div>
                    <div
                        class="flaged d-flex flex-row flex-wrap align-items-center pt-4 pb-4"
                    >
                        <a href="#">
                            <img
                                src="{{asset('site/assets/img/flage1.png')}}"
                                alt="نماد الکترونیکی"
                                class="p-0 m-0"
                            />
                        </a>
                        <a href="#">
                            <img
                                src="{{asset('site/assets/img/flage2.png')}}"
                                alt="اتحادیه ی کشوری"
                                class="p-0 m-0"
                            />
                        </a>
                        <a href="#">
                            <img
                                src="{{asset('site/assets/img/flage3.png')}}"
                                alt="ثبت ملی"
                                class="p-0 m-0"
                            />
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- BasePage section -->
        <div class="col-12 BasePage d-flex flex-column">
            <!-- haeder site -->
            <div
                class="BasePage-top col-12 d-flex align-items-center justify-content-between"
            >
                <div class="logo-wrapper">
                    <img src="{{ asset($logo) }}" alt="{{ $title }}" />
                </div>
                <div class="notification-Bar position-relative d-flex">
                    <div class="d-flex align-items-center">
              <span class="material-icons unread cursPointer"
              >bookmark_border</span
              >
                        <span class="material-icons cursPointer d-md-block d-none"
                        >chat_bubble_outline</span
                        >
                        <span class="material-icons cursPointer">notifications_none</span>
                        <span
                            class="material-icons cursPointer d-md-block d-none"
                            onclick="showAccountInfo()"
                        >person_outline</span
                        >

                        <!-- Account info has show when click on person icon -->
                        <div class="Account-info d-none flex-column p-2">
                            <div class="Line"></div>
                            <div class="top-of-Account-info d-flex align-items-center">
                                <div
                                    class="right-top-of-Account-info d-flex align-items-center"
                                >
                                    <img
                                        src="{{ auth()->check() ? asset(auth()->user()->profile_image) : asset('site/assets/img/ProfilePhoto.png')}}"
                                        class="cursPointer"
                                        alt=""
                                    />
                                    <div class="d-flex flex-column p-2">
                                        <div class="d-flex align-items-center">
                                            <p class="p-0 m-0 bold">سلام</p>
                                            <p class="Account-name2 cursPointer">{{!empty(auth()->user()->first_name) ? auth()->user()->first_name : auth()->user()->user_name}}</p>
                                        </div>
                                        <p class="m-0 p-0 des-of-Product">خوش اومدی</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Go to Profile btn -->
                            <div wire:click="goTo('user.profile')" class="Profile-Account-info d-flex align-items-center justify-content-between p-2 cursPointer">
                                <p class="m-0 p-0 purple">مشاهده پروفایل</p>
                                <div class="material-icons purple">chevron_left</div>
                            </div>
                            <!-- two btn for Auth -->
                            <div class="d-flex flex-column p-2 Auth-section-Account-info">
                                <p class="m-0 p-0 cursPointer">احراز هویت</p>
                                <p class="m-0 p-0 pt-3 cursPointer">آگهی های من</p>
                            </div>
                            <!-- Log out -->
                            <div class="d-flex align-items-center p-2 pt-3 cursPointer">
                                <div class="material-icons">logout</div>
                                <p class="p-0 m-0">خروج از حساب</p>
                            </div>
                        </div>
                    </div>
                    <div
                        class="phone-number d-flex align-items-center d-md-flex d-none"
                    >
                        <p class="m-0 p-0">0515222222</p>
                    </div>
                </div>
            </div>

            <!-- Search Box -->
            <div class="Search-bar d-flex flex-row justify-content-start">
                <div class="search d-flex align-items-center justify-content-around">
                    <span class="material-icons"> search </span>
                    <input type="text" placeholder="جستجو در تمام اکانت ها " />
                    <button class="Cbtn search-btn">جستجو</button>
                </div>
            </div>

            <!--Top rated -->
            <div class="Top-rated d-flex flex-column justify-content-start d-md-block d-none p-4" wire:ignore>
                <p>بیشترین معامله</p>
                <div class="d-flex flex-wrap align-items-center">
                    <div class="swiper-container pb-4">
                        <div class="swiper-wrapper">
                            @foreach($most_categories as $item)
                                <div class="swiper-slide p-0 m-0">
                                    <div class="d-flex flex-column align-items-center">
                                        <img src="{{asset($item->slider)}}" alt="{{ $item->title }}" />
                                        <p class="name-of-Product m-0 {{ in_array($item->id,$category) ? 'text-black' : '' }}">{{ $item->title }}</p>
                                        <div class="d-flex align-items-center">
                                            <p class="des-of-Product m-0">تعداد آگهی ها</p>
                                            <p class="des-of-Product m-0">:</p>
                                            <p class="des-of-Product m-0">{{ $item->orders()->count() }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productes -->
            <div class="Prodcts d-flex flex-row align-items-center flex-wrap p-4 w-100 mb-5">
                <div class="col-12 col-sm-6 col-md-6 col-xl-3">
                    <div class="Products-item d-flex flex-column">
                        <div
                            class="Top-Products-item d-flex align-items-center justify-content-between w-100 p-2"
                        >
                            <div class="Right-Top-Products-item d-flex align-items-center">
                                <div class="spacialAccount Pro-wrapper">
                                    <img
                                        src="{{asset('site/assets/img/Pro.png')}}"
                                        alt=""
                                        class="cursPointer"
                                    />
                                </div>
                                <div class="d-flex flex-column">
                                    <p class="Account-name m-0">ralia</p>
                                    <p class="Last-Seen m-0">آخرین بازدید به تازگی</p>
                                </div>
                            </div>
                            <div class="left-Top-Products-item">
                                <span class="material-icons cursPointer">more_horiz</span>
                            </div>
                        </div>
                        <div class="Base-Products-item p-2">
                            <img src="{{asset('site/assets/img/call-of-duty.png')}}" alt="" class="w-100" />
                            <div
                                class="categori d-flex flex-column align-items-center justify-content-center"
                            >
                                cod mobile
                            </div>
                        </div>
                        <div class="bottom-Products-item d-flex flex-column pt-2">
                            <p class="Product-des">
                                اکانت فورتنایت از سیزن دو اکانت فورتنایت از سیزن دو
                            </p>
                            <div
                                class="bottom-bottom-Products-item d-flex align-items-center justify-content-between"
                            >
                                <div class="right-bottom-bottom-Products-item cursPointer">
                                    <img src="{{asset('site/assets/img/windows.png')}}" alt="" />
                                </div>
                                <div
                                    class="left-bottom-bottom-Products-item d-flex flex-column"
                                >
                                    <p class="m-0 p-0 type_price">تومان</p>
                                    <p class="m-0 p-0 Price">15000000</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-xl-3">
                    <div class="Products-item d-flex flex-column">
                        <div
                            class="Top-Products-item d-flex align-items-center justify-content-between w-100 p-2"
                        >
                            <div class="Right-Top-Products-item d-flex align-items-center">
                                <div class="spacialAccount Pro-wrapper">
                                    <img
                                        src="{{asset('site/assets/img/Pro.png')}}"
                                        alt=""
                                        class="cursPointer"
                                    />
                                </div>
                                <div class="d-flex flex-column">
                                    <p class="Account-name m-0">ralia</p>
                                    <p class="Last-Seen m-0">آخرین بازدید به تازگی</p>
                                </div>
                            </div>
                            <div class="left-Top-Products-item">
                                <span class="material-icons cursPointer">more_horiz</span>
                            </div>
                        </div>
                        <div class="Base-Products-item p-2">
                            <img src="{{asset('site/assets/img/call-of-duty.png')}}" alt="" class="w-100" />
                            <div
                                class="categori d-flex flex-column align-items-center justify-content-center"
                            >
                                cod mobile
                            </div>
                        </div>
                        <div class="bottom-Products-item d-flex flex-column pt-2">
                            <p class="Product-des">
                                اکانت فورتنایت از سیزن دو اکانت فورتنایت از سیزن دو
                            </p>
                            <div
                                class="bottom-bottom-Products-item d-flex align-items-center justify-content-between"
                            >
                                <div class="right-bottom-bottom-Products-item cursPointer">
                                    <img src="{{asset('site/assets/img/windows.png')}}" alt="" />
                                </div>
                                <div
                                    class="left-bottom-bottom-Products-item d-flex flex-column"
                                >
                                    <p class="m-0 p-0 type_price">تومان</p>
                                    <p class="m-0 p-0 Price">15000000</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-xl-3">
                    <div class="Products-item d-flex flex-column">
                        <div
                            class="Top-Products-item d-flex align-items-center justify-content-between w-100 p-2"
                        >
                            <div class="Right-Top-Products-item d-flex align-items-center">
                                <div class="spacialAccount Pro-wrapper">
                                    <img
                                        src="{{asset('site/assets/img/Pro.png')}}"
                                        alt=""
                                        class="cursPointer"
                                    />
                                </div>
                                <div class="d-flex flex-column">
                                    <p class="Account-name m-0">ralia</p>
                                    <p class="Last-Seen m-0">آخرین بازدید به تازگی</p>
                                </div>
                            </div>
                            <div class="left-Top-Products-item">
                                <span class="material-icons cursPointer">more_horiz</span>
                            </div>
                        </div>
                        <div class="Base-Products-item p-2">
                            <img src="{{asset('site/assets/img/call-of-duty.png')}}" alt="" class="w-100" />
                            <div
                                class="categori d-flex flex-column align-items-center justify-content-center"
                            >
                                cod mobile
                            </div>
                        </div>
                        <div class="bottom-Products-item d-flex flex-column pt-2">
                            <p class="Product-des">
                                اکانت فورتنایت از سیزن دو اکانت فورتنایت از سیزن دو
                            </p>
                            <div
                                class="bottom-bottom-Products-item d-flex align-items-center justify-content-between"
                            >
                                <div class="right-bottom-bottom-Products-item cursPointer">
                                    <img src="{{asset('site/assets/img/windows.png')}}" alt="" />
                                </div>
                                <div
                                    class="left-bottom-bottom-Products-item d-flex flex-column"
                                >
                                    <p class="m-0 p-0 type_price">تومان</p>
                                    <p class="m-0 p-0 Price">15000000</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-xl-3">
                    <div class="Products-item d-flex flex-column">
                        <div
                            class="Top-Products-item d-flex align-items-center justify-content-between w-100 p-2"
                        >
                            <div class="Right-Top-Products-item d-flex align-items-center">
                                <div class="spacialAccount Pro-wrapper">
                                    <img
                                        src="{{asset('site/assets/img/Pro.png')}}"
                                        alt=""
                                        class="cursPointer"
                                    />
                                </div>
                                <div class="d-flex flex-column">
                                    <p class="Account-name m-0">ralia</p>
                                    <p class="Last-Seen m-0">آخرین بازدید به تازگی</p>
                                </div>
                            </div>
                            <div class="left-Top-Products-item">
                                <span class="material-icons cursPointer">more_horiz</span>
                            </div>
                        </div>
                        <div class="Base-Products-item p-2">
                            <img src="{{asset('site/assets/img/call-of-duty.png')}}" alt="" class="w-100" />
                            <div
                                class="categori d-flex flex-column align-items-center justify-content-center"
                            >
                                cod mobile
                            </div>
                        </div>
                        <div class="bottom-Products-item d-flex flex-column pt-2">
                            <p class="Product-des">
                                اکانت فورتنایت از سیزن دو اکانت فورتنایت از سیزن دو
                            </p>
                            <div
                                class="bottom-bottom-Products-item d-flex align-items-center justify-content-between"
                            >
                                <div class="right-bottom-bottom-Products-item cursPointer">
                                    <img src="{{asset('site/assets/img/windows.png')}}" alt="" />
                                </div>
                                <div
                                    class="left-bottom-bottom-Products-item d-flex flex-column"
                                >
                                    <p class="m-0 p-0 type_price">تومان</p>
                                    <p class="m-0 p-0 Price">15000000</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-xl-3">
                    <div class="Products-item d-flex flex-column">
                        <div
                            class="Top-Products-item d-flex align-items-center justify-content-between w-100 p-2"
                        >
                            <div class="Right-Top-Products-item d-flex align-items-center">
                                <div class="spacialAccount Pro-wrapper">
                                    <img
                                        src="{{asset('site/assets/img/Pro.png')}}"
                                        alt=""
                                        class="cursPointer"
                                    />
                                </div>
                                <div class="d-flex flex-column">
                                    <p class="Account-name m-0">ralia</p>
                                    <p class="Last-Seen m-0">آخرین بازدید به تازگی</p>
                                </div>
                            </div>
                            <div class="left-Top-Products-item">
                                <span class="material-icons cursPointer">more_horiz</span>
                            </div>
                        </div>
                        <div class="Base-Products-item p-2">
                            <img src="{{asset('site/assets/img/call-of-duty.png')}}" alt="" class="w-100" />
                            <div
                                class="categori d-flex flex-column align-items-center justify-content-center"
                            >
                                cod mobile
                            </div>
                        </div>
                        <div class="bottom-Products-item d-flex flex-column pt-2">
                            <p class="Product-des">
                                اکانت فورتنایت از سیزن دو اکانت فورتنایت از سیزن دو
                            </p>
                            <div
                                class="bottom-bottom-Products-item d-flex align-items-center justify-content-between"
                            >
                                <div class="right-bottom-bottom-Products-item cursPointer">
                                    <img src="{{asset('site/assets/img/windows.png')}}" alt="" />
                                </div>
                                <div
                                    class="left-bottom-bottom-Products-item d-flex flex-column"
                                >
                                    <p class="m-0 p-0 type_price">تومان</p>
                                    <p class="m-0 p-0 Price">15000000</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-xl-3">
                    <div class="Products-item d-flex flex-column">
                        <div
                            class="Top-Products-item d-flex align-items-center justify-content-between w-100 p-2"
                        >
                            <div class="Right-Top-Products-item d-flex align-items-center">
                                <div class="spacialAccount Pro-wrapper">
                                    <img
                                        src="{{asset('site/assets/img/Pro.png')}}"
                                        alt=""
                                        class="cursPointer"
                                    />
                                </div>
                                <div class="d-flex flex-column">
                                    <p class="Account-name m-0">ralia</p>
                                    <p class="Last-Seen m-0">آخرین بازدید به تازگی</p>
                                </div>
                            </div>
                            <div class="left-Top-Products-item">
                                <span class="material-icons cursPointer">more_horiz</span>
                            </div>
                        </div>
                        <div class="Base-Products-item p-2">
                            <img src="{{asset('site/assets/img/call-of-duty.png')}}" alt="" class="w-100" />
                            <div
                                class="categori d-flex flex-column align-items-center justify-content-center"
                            >
                                cod mobile
                            </div>
                        </div>
                        <div class="bottom-Products-item d-flex flex-column pt-2">
                            <p class="Product-des">
                                اکانت فورتنایت از سیزن دو اکانت فورتنایت از سیزن دو
                            </p>
                            <div
                                class="bottom-bottom-Products-item d-flex align-items-center justify-content-between"
                            >
                                <div class="right-bottom-bottom-Products-item cursPointer">
                                    <img src="{{asset('site/assets/img/windows.png')}}" alt="" />
                                </div>
                                <div
                                    class="left-bottom-bottom-Products-item d-flex flex-column"
                                >
                                    <p class="m-0 p-0 type_price">تومان</p>
                                    <p class="m-0 p-0 Price">15000000</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-xl-3">
                    <div class="Products-item d-flex flex-column">
                        <div
                            class="Top-Products-item d-flex align-items-center justify-content-between w-100 p-2"
                        >
                            <div class="Right-Top-Products-item d-flex align-items-center">
                                <div class="spacialAccount Pro-wrapper">
                                    <img
                                        src="{{asset('site/assets/img/Pro.png')}}"
                                        alt=""
                                        class="cursPointer"
                                    />
                                </div>
                                <div class="d-flex flex-column">
                                    <p class="Account-name m-0">ralia</p>
                                    <p class="Last-Seen m-0">آخرین بازدید به تازگی</p>
                                </div>
                            </div>
                            <div class="left-Top-Products-item">
                                <span class="material-icons cursPointer">more_horiz</span>
                            </div>
                        </div>
                        <div class="Base-Products-item p-2">
                            <img src="{{asset('site/assets/img/call-of-duty.png')}}" alt="" class="w-100" />
                            <div
                                class="categori d-flex flex-column align-items-center justify-content-center"
                            >
                                cod mobile
                            </div>
                        </div>
                        <div class="bottom-Products-item d-flex flex-column pt-2">
                            <p class="Product-des">
                                اکانت فورتنایت از سیزن دو اکانت فورتنایت از سیزن دو
                            </p>
                            <div
                                class="bottom-bottom-Products-item d-flex align-items-center justify-content-between"
                            >
                                <div class="right-bottom-bottom-Products-item cursPointer">
                                    <img src="{{asset('site/assets/img/windows.png')}}" alt="" />
                                </div>
                                <div
                                    class="left-bottom-bottom-Products-item d-flex flex-column"
                                >
                                    <p class="m-0 p-0 type_price">تومان</p>
                                    <p class="m-0 p-0 Price">15000000</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-xl-3">
                    <div class="Products-item d-flex flex-column">
                        <div
                            class="Top-Products-item d-flex align-items-center justify-content-between w-100 p-2"
                        >
                            <div class="Right-Top-Products-item d-flex align-items-center">
                                <div class="spacialAccount Pro-wrapper">
                                    <img
                                        src="{{asset('site/assets/img/Pro.png')}}"
                                        alt=""
                                        class="cursPointer"
                                    />
                                </div>
                                <div class="d-flex flex-column">
                                    <p class="Account-name m-0">ralia</p>
                                    <p class="Last-Seen m-0">آخرین بازدید به تازگی</p>
                                </div>
                            </div>
                            <div class="left-Top-Products-item">
                                <span class="material-icons cursPointer">more_horiz</span>
                            </div>
                        </div>
                        <div class="Base-Products-item p-2">
                            <img src="{{asset('site/assets/img/call-of-duty.png')}}" alt="" class="w-100" />
                            <div
                                class="categori d-flex flex-column align-items-center justify-content-center"
                            >
                                cod mobile
                            </div>
                        </div>
                        <div class="bottom-Products-item d-flex flex-column pt-2">
                            <p class="Product-des">
                                اکانت فورتنایت از سیزن دو اکانت فورتنایت از سیزن دو
                            </p>
                            <div
                                class="bottom-bottom-Products-item d-flex align-items-center justify-content-between"
                            >
                                <div class="right-bottom-bottom-Products-item cursPointer">
                                    <img src="{{asset('site/assets/img/windows.png')}}" alt="" />
                                </div>
                                <div
                                    class="left-bottom-bottom-Products-item d-flex flex-column"
                                >
                                    <p class="m-0 p-0 type_price">تومان</p>
                                    <p class="m-0 p-0 Price">15000000</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom navigation Bar -->
            <div
                class="d-flex d-md-none w-100 position-fixed
           bottom-0 bottomNavigation row align-items-center p-1 m-0 justify-content-around"
            >
                <div
                    class="w-20 d-flex flex-column align-items-center"
                    onclick="NavHandler(0)"
                >
                    <p class="p-0 m-0 nav-items navigationBottom navActive">آگهی</p>
                </div>
                <div
                    class="w-20 d-flex flex-column align-items-center"
                    onclick="NavHandler(1)"
                >
                    <span class="material-icons navigationBottom">grid_view</span>
                    <p class="p-0 m-0 nav-items">دسته بندی</p>
                </div>
                <div
                    class="w-20 d-flex flex-column align-items-center"
                    onclick="NavHandler(2)"
                >
                    <span class="material-icons navigationBottom">add_box</span>
                    <p class="p-0 m-0 nav-items">ثبت آگهی</p>
                </div>
                <div
                    class="w-20 d-flex flex-column align-items-center"
                    onclick="NavHandler(3)"
                >
                    <span class="material-icons navigationBottom">chat</span>
                    <p class="p-0 m-0 nav-items">چت</p>
                </div>
                <div
                    class="w-20 d-flex flex-column align-items-center"
                    onclick="NavHandler(4)"
                >
                    <span class="material-icons navigationBottom">person_outline </span>
                    <p class="p-0 m-0 nav-items">پروفایل</p>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function setCategory(id) {
            console.log(1);
            @this.call('setCategory', id)
        }
        $(document).ready(function() {
            new Swiper('.swiper-container', {
                loop: true,
                nextButton: '.d-none',
                prevButton: '.d-none',
                slidesPerView: 6,
                paginationClickable: true,
                spaceBetween: 30,
                breakpoints: {
                    1920: {
                        slidesPerView: 8,
                        spaceBetween: 55
                    },
                    1600: {
                        slidesPerView: 6,
                        spaceBetween: 55
                    },
                    1400: {
                        slidesPerView: 5,
                        spaceBetween: 0
                    },
                    1200: {
                        slidesPerView: 5,
                        spaceBetween: 10
                    },
                    1020: {
                        slidesPerView: 4,
                        spaceBetween: 10
                    },
                    820: {
                        slidesPerView: 3,
                        spaceBetween: 10
                    },
                    250: {
                        slidesPerView: 2,
                        spaceBetween: 10
                    },
                    1: {
                        slidesPerView: 1,
                        spaceBetween: 250
                    }
                }
            });
        });
    </script>
@endpush
