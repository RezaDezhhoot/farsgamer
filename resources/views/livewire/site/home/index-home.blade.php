<div>
    <div class="all d-flex flex-lg-row flex-column">
        <div class="sideBar col-md-3 d-none d-md-flex flex-column">
            <!-- SideBar top -->
            <div
                class="sideBar-top d-flex flex-column justify-content-start align-items-center mt-5"
            >
                <img
                    src="{{ asset('site/assets/img/ProfilePhoto.png')}}"
                    alt="ProfilePhoto"
                    class="ProfilePhoto"
                />
                <!-- SideBar-top text icon -->
                <div class="sideBar-top-text d-flex flex-row mt-2">
                    <span class="material-icons color-gray"> person_outline </span>
                    <a class="sideBar-top-text-a color-gray" href="#">ورود</a>
                    <a class="sideBar-top-text-a color-gray" href="#">/ثبت نام</a>
                </div>
                <!-- SideBar btn section -->
                <div class="sideBar-bts d-flex flex-row justify-content-center">
                    <button
                        class="Cbtn d-flex flex-row align-items-center justify-content-center mt-2"
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

                    <div
                        class="d-flex flex-row justify-content-start align-items-center mb-3 cursPointer"
                        data-bs-toggle="collapse"
                        data-bs-target="#SteamCollaps"
                        aria-expanded="false"
                        aria-controls="SteamCollaps"
                    >
                        <img src="{{ asset('site/assets/img/steam.png')}}" alt="steam logo" />
                        <p class="p-0 side-item">استیم</p>
                    </div>
                    <div class="collapse" id="SteamCollaps">
                        <ul class="filter-name">
                            <li class="cursPointer" onclick="ActiveDeActive(this)">ali</li>
                            <li class="cursPointer" onclick="ActiveDeActive(this)">reza</li>
                        </ul>
                    </div>

                    <div
                        class="d-flex flex-row justify-content-start align-items-center mb-3 cursPointer"
                        data-bs-toggle="collapse"
                        data-bs-target="#OrginCollaps"
                        aria-expanded="false"
                        aria-controls="OrginCollaps"
                    >
                        <img src="{{ asset('site/assets/img/origin.png')}}" alt="origin logo" />
                        <p class="p-0 side-item">اورجین</p>
                    </div>
                    <div class="collapse" id="OrginCollaps">
                        <ul class="filter-name">
                            <li class="cursPointer" onclick="ActiveDeActive(this)">
                                بتل فیلد
                            </li>
                            <li class="cursPointer" onclick="ActiveDeActive(this)">csGo</li>
                            <li class="cursPointer" onclick="ActiveDeActive(this)">
                                fifa 2022
                            </li>
                            <li class="cursPointer" onclick="ActiveDeActive(this)">csGo</li>
                            <li class="cursPointer" onclick="ActiveDeActive(this)">
                                بتل فیلد
                            </li>
                            <li class="cursPointer" onclick="ActiveDeActive(this)">
                                fifa 2022
                            </li>
                        </ul>
                    </div>

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
                            <p class="m-0 p-0">بتل پس</p>
                        </div>
                        <div class="collapse" id="filterItem1">
                            <label
                                class="d-flex flex-row align-items-center cursPointer filterItem-items"
                            >
                                <label class="CustomeCheckBox">
                                    <input type="radio" name="check" />
                                    <span class="checkmark"></span>
                                </label>
                                <p class="p-0 m-0">موجود است</p>
                            </label>
                            <label
                                class="d-flex flex-row align-items-center cursPointer filterItem-items"
                            >
                                <label class="CustomeCheckBox">
                                    <input type="radio" name="check" />
                                    <span class="checkmark"></span>
                                </label>
                                <p class="p-0 m-0">موجود نیست</p>
                            </label>
                        </div>
                    </div>

                    <!-- test for toggle switch -->
                    <div class="filteringCollaps d-flex flex-column">
                        <div
                            class="filterItem d-flex flex-row align-items-center pt-2 pb-2 cursPointer"
                            data-bs-toggle="collapse"
                            data-bs-target="#filterItem2"
                            aria-expanded="false"
                            aria-controls="filterItem2"
                        >
                            <span class="material-icons">expand_more</span>
                            <p class="m-0 p-0">بتل پس</p>
                        </div>
                        <div class="collapse" id="filterItem2">
                            <div
                                class="d-flex flex-row align-items-center justify-content-between pb-3 pt-2"
                            >
                                <p class="p-0 m-0">فقط کالاهای موجود</p>
                                <div>
                                    <input type="checkbox" class="switch-Custom" id="switch" />
                                    <label class="Custom-toggle-switch" for="switch"></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- test for Custom rangeInput -->
                    <!-- <div class="filteringCollaps d-flex flex-column">
                      <div
                        class="filterItem d-flex flex-row align-items-center pt-2 pb-2 cursPointer"
                        data-bs-toggle="collapse"
                        data-bs-target="#filterItem3"
                        aria-expanded="false"
                        aria-controls="filterItem3"
                      >
                        <span class="material-icons">expand_more</span>
                        <p class="m-0 p-0">بتل پس</p>
                      </div>
                      <div class="collapse" id="filterItem3">
                        <div>
                          <input type="range" />
                        </div>
                      </div>
                    </div> -->

                    <div class="filteringCollaps d-flex flex-row flex-wrap">
                        <a href="#">پشتیبانی</a>
                        <a href="#">قوانین</a>
                        <a href="#">ارتباط با ما</a>
                        <a href="#">سوالات متداول</a>
                        <a href="#">درباره ما</a>
                    </div>

                    <div
                        class="socialMedia d-flex flex-row flex-wrap justify-content-around pt-3"
                    >
                        <img
                            class="cursPointer"
                            src="{{ asset('site/assets/img/youtube.svg')}}"
                            alt="youtube logo"
                        />
                        <img
                            class="cursPointer"
                            src="{{ asset('site/assets/img/instagram.svg')}}"
                            alt=""
                        />
                        <span class="material-icons cursPointer">telegram</span>
                        <span class="material-icons cursPointer">discord</span>
                    </div>
                    <div
                        class="flaged d-flex flex-row flex-wrap align-items-center pt-4 pb-4"
                    >
                        <a href="#">
                            <img
                                src="{{ asset('site/assets/img/flage1.png')}}"
                                alt="نماد الکترونیکی"
                                class="p-0 m-0"
                            />
                        </a>
                        <a href="#">
                            <img
                                src="{{ asset('site/assets/img/flage2.png')}}"
                                alt="اتحادیه ی کشوری"
                                class="p-0 m-0"
                            />
                        </a>
                        <a href="#">
                            <img
                                src="{{ asset('site/assets/img/flage3.png')}}"
                                alt="ثبت ملی"
                                class="p-0 m-0"
                            />
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-9 d-flex flex-column">
            <!-- haeder site -->
            <div
                class="BasePage-top col-12 d-flex align-items-center justify-content-between"
            >
                <div class="logo-wrapper">
                    <img src="{{ asset('site/assets/img/Logo.png')}}" alt="" />
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
                                        src="{{ asset('site/assets/img/ProfilePhoto.png')}}"
                                        class="cursPointer"
                                        alt=""
                                    />
                                    <div class="d-flex flex-column p-2">
                                        <div class="d-flex align-items-center">
                                            <p class="p-0 m-0 bold">سلام</p>
                                            <p class="Account-name2 cursPointer">محمد رضا</p>
                                        </div>
                                        <p class="m-0 p-0 des-of-Product">خوش اومدی</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Go to Profile btn -->
                            <div
                                class="Profile-Account-info d-flex align-items-center justify-content-between p-2 cursPointer"
                            >
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
            <div
                class="Top-rated d-flex flex-column justify-content-start d-md-block d-none p-4"
            >
                <p>بیشترین معامله</p>
                <div class="d-flex flex-wrap align-items-center">
                    <div
                        class="Top-rated-item d-flex flex-column align-items-center cursPointer"
                    >
                        <img src="{{ asset('site/assets/img/Battlefield.png')}}" alt="" />
                        <p class="name-of-Product m-0">battlefield 2024</p>
                        <div class="d-flex align-items-center">
                            <p class="des-of-Product m-0">تعداد آگهی ها</p>
                            <p class="des-of-Product m-0">:</p>
                            <p class="des-of-Product m-0">1080</p>
                        </div>
                    </div>
                    <div
                        class="Top-rated-item d-flex flex-column align-items-center cursPointer"
                    >
                        <img src="{{ asset('site/assets/img/Battlefield.png')}}" alt="" />
                        <p class="name-of-Product m-0">battlefield 2024</p>
                        <div class="d-flex align-items-center">
                            <p class="des-of-Product m-0">تعداد آگهی ها</p>
                            <p class="des-of-Product m-0">:</p>
                            <p class="des-of-Product m-0">1080</p>
                        </div>
                    </div>
                    <div
                        class="Top-rated-item d-flex flex-column align-items-center cursPointer"
                    >
                        <img src="{{ asset('site/assets/img/Battlefield.png')}}" alt="" />
                        <p class="name-of-Product m-0">battlefield 2024</p>
                        <div class="d-flex align-items-center">
                            <p class="des-of-Product m-0">تعداد آگهی ها</p>
                            <p class="des-of-Product m-0">:</p>
                            <p class="des-of-Product m-0">1080</p>
                        </div>
                    </div>
                    <div
                        class="Top-rated-item d-flex flex-column align-items-center cursPointer"
                    >
                        <img src="{{ asset('site/assets/img/Battlefield.png')}}" alt="" />
                        <p class="name-of-Product m-0">battlefield 2024</p>
                        <div class="d-flex align-items-center">
                            <p class="des-of-Product m-0">تعداد آگهی ها</p>
                            <p class="des-of-Product m-0">:</p>
                            <p class="des-of-Product m-0">1080</p>
                        </div>
                    </div>
                    <div
                        class="Top-rated-item d-flex flex-column align-items-center cursPointer"
                    >
                        <img src="{{ asset('site/assets/img/Battlefield.png')}}" alt="" />
                        <p class="name-of-Product m-0">battlefield 2024</p>
                        <div class="d-flex align-items-center">
                            <p class="des-of-Product m-0">تعداد آگهی ها</p>
                            <p class="des-of-Product m-0">:</p>
                            <p class="des-of-Product m-0">1080</p>
                        </div>
                    </div>
                    <div
                        class="Top-rated-item d-flex flex-column align-items-center cursPointer"
                    >
                        <img src="{{ asset('site/assets/img/Battlefield.png')}}" alt="" />
                        <p class="name-of-Product m-0">battlefield 2024</p>
                        <div class="d-flex align-items-center">
                            <p class="des-of-Product m-0">تعداد آگهی ها</p>
                            <p class="des-of-Product m-0">:</p>
                            <p class="des-of-Product m-0">1080</p>
                        </div>
                    </div>
                    <div
                        class="Top-rated-item d-flex flex-column align-items-center cursPointer"
                    >
                        <img src="{{ asset('site/assets/img/Battlefield.png')}}" alt="" />
                        <p class="name-of-Product m-0">battlefield 2024</p>
                        <div class="d-flex align-items-center">
                            <p class="des-of-Product m-0">تعداد آگهی ها</p>
                            <p class="des-of-Product m-0">:</p>
                            <p class="des-of-Product m-0">1080</p>
                        </div>
                    </div>
                    <div
                        class="Top-rated-item d-flex flex-column align-items-center cursPointer"
                    >
                        <img src="{{ asset('site/assets/img/Battlefield.png')}}" alt="" />
                        <p class="name-of-Product m-0">battlefield 2024</p>
                        <div class="d-flex align-items-center">
                            <p class="des-of-Product m-0">تعداد آگهی ها</p>
                            <p class="des-of-Product m-0">:</p>
                            <p class="des-of-Product m-0">1080</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productes -->
            <div
                class="Prodcts d-flex flex-row align-items-center flex-wrap p-4 w-100"
            >
                <div class="col-12 col-md-3">
                    <div class="Products-item d-flex flex-column mb-4">
                        <div
                            class="Top-Products-item d-flex align-items-center justify-content-between w-100 p-2"
                        >
                            <div class="Right-Top-Products-item d-flex align-items-center">
                                <div class="spacialAccount Pro-wrapper">
                                    <img
                                        src="{{ asset('site/assets/img/Pro.png')}}"
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
                            <img src="{{ asset('site/assets/img/call-of-duty.png')}}" alt="" class="w-100" />
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
                                    <img src="{{ asset('site/assets/img/windows.png')}}" alt="" />
                                </div>
                                <div
                                    class="left-bottom-bottom-Products-item d-flex flex-column"
                                >
                                    <p class="m-0 p-0">تومان</p>
                                    <p class="m-0 p-0 Price">15000000</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="Products-item d-flex flex-column mb-4">
                        <div
                            class="Top-Products-item d-flex align-items-center justify-content-between w-100 p-2"
                        >
                            <div class="Right-Top-Products-item d-flex align-items-center">
                                <div class="spacialAccount Pro-wrapper">
                                    <img
                                        src="{{ asset('site/assets/img/Pro.png')}}"
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
                            <img src="{{ asset('site/assets/img/call-of-duty.png')}}" alt="" class="w-100" />
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
                                    <img src="{{ asset('site/assets/img/windows.png')}}" alt="" />
                                </div>
                                <div
                                    class="left-bottom-bottom-Products-item d-flex flex-column"
                                >
                                    <p class="m-0 p-0">تومان</p>
                                    <p class="m-0 p-0 Price">15000000</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="Products-item d-flex flex-column mb-4">
                        <div
                            class="Top-Products-item d-flex align-items-center justify-content-between w-100 p-2"
                        >
                            <div class="Right-Top-Products-item d-flex align-items-center">
                                <div class="spacialAccount Pro-wrapper">
                                    <img
                                        src="{{ asset('site/assets/img/Pro.png')}}"
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
                            <img src="{{ asset('site/assets/img/call-of-duty.png')}}" alt="" class="w-100" />
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
                                    <img src="{{ asset('site/assets/img/windows.png')}}" alt="" />
                                </div>
                                <div
                                    class="left-bottom-bottom-Products-item d-flex flex-column"
                                >
                                    <p class="m-0 p-0">تومان</p>
                                    <p class="m-0 p-0 Price">15000000</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="Products-item d-flex flex-column mb-4">
                        <div
                            class="Top-Products-item d-flex align-items-center justify-content-between w-100 p-2"
                        >
                            <div class="Right-Top-Products-item d-flex align-items-center">
                                <div class="spacialAccount Pro-wrapper">
                                    <img
                                        src="{{ asset('site/assets/img/Pro.png')}}"
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
                            <img src="{{ asset('site/assets/img/call-of-duty.png')}}" alt="" class="w-100" />
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
                                    <img src="{{ asset('site/assets/img/windows.png')}}" alt="" />
                                </div>
                                <div
                                    class="left-bottom-bottom-Products-item d-flex flex-column"
                                >
                                    <p class="m-0 p-0">تومان</p>
                                    <p class="m-0 p-0 Price">15000000</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="Products-item d-flex flex-column mb-4">
                        <div
                            class="Top-Products-item d-flex align-items-center justify-content-between w-100 p-2"
                        >
                            <div class="Right-Top-Products-item d-flex align-items-center">
                                <div class="spacialAccount Pro-wrapper">
                                    <img
                                        src="{{ asset('site/assets/img/Pro.png')}}"
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
                            <img src="{{ asset('site/assets/img/call-of-duty.png')}}" alt="" class="w-100" />
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
                                    <img src="{{ asset('site/assets/img/windows.png')}}" alt="" />
                                </div>
                                <div
                                    class="left-bottom-bottom-Products-item d-flex flex-column"
                                >
                                    <p class="m-0 p-0">تومان</p>
                                    <p class="m-0 p-0 Price">15000000</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="Products-item d-flex flex-column mb-4">
                        <div
                            class="Top-Products-item d-flex align-items-center justify-content-between w-100 p-2"
                        >
                            <div class="Right-Top-Products-item d-flex align-items-center">
                                <div class="spacialAccount Pro-wrapper">
                                    <img
                                        src="{{ asset('site/assets/img/Pro.png')}}"
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
                            <img src="{{ asset('site/assets/img/call-of-duty.png')}}" alt="" class="w-100" />
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
                                    <img src="{{ asset('site/assets/img/windows.png')}}" alt="" />
                                </div>
                                <div
                                    class="left-bottom-bottom-Products-item d-flex flex-column"
                                >
                                    <p class="m-0 p-0">تومان</p>
                                    <p class="m-0 p-0 Price">15000000</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="Products-item d-flex flex-column mb-4">
                        <div
                            class="Top-Products-item d-flex align-items-center justify-content-between w-100 p-2"
                        >
                            <div class="Right-Top-Products-item d-flex align-items-center">
                                <div class="spacialAccount Pro-wrapper">
                                    <img
                                        src="{{ asset('site/assets/img/Pro.png')}}"
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
                            <img src="{{ asset('site/assets/img/call-of-duty.png')}}" alt="" class="w-100" />
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
                                    <img src="{{ asset('site/assets/img/windows.png')}}" alt="" />
                                </div>
                                <div
                                    class="left-bottom-bottom-Products-item d-flex flex-column"
                                >
                                    <p class="m-0 p-0">تومان</p>
                                    <p class="m-0 p-0 Price">15000000</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="Products-item d-flex flex-column mb-4">
                        <div
                            class="Top-Products-item d-flex align-items-center justify-content-between w-100 p-2"
                        >
                            <div class="Right-Top-Products-item d-flex align-items-center">
                                <div class="spacialAccount Pro-wrapper">
                                    <img
                                        src="{{ asset('site/assets/img/Pro.png')}}"
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
                            <img src="{{ asset('site/assets/img/call-of-duty.png')}}" alt="" class="w-100" />
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
                                    <img src="{{ asset('site/assets/img/windows.png')}}" alt="" />
                                </div>
                                <div
                                    class="left-bottom-bottom-Products-item d-flex flex-column"
                                >
                                    <p class="m-0 p-0">تومان</p>
                                    <p class="m-0 p-0 Price">15000000</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="Products-item d-flex flex-column col-12 col-md-3 mb-4">
                  <div
                    class="Top-Products-item d-flex align-items-center justify-content-between w-100 p-2"
                  >
                    <div class="Right-Top-Products-item d-flex align-items-center">
                      <div class="spacialAccount Pro-wrapper">
                        <img src="./assets/img/Pro.png" alt="" class="cursPointer" />
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
                    <img src="./assets/img/call-of-duty.png" alt="" class="w-100" />
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
                        <img src="./assets/img/windows.png" alt="" />
                      </div>
                      <div
                        class="left-bottom-bottom-Products-item d-flex flex-column"
                      >
                        <p class="m-0 p-0">تومان</p>
                        <p class="m-0 p-0 Price">15000000</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="Products-item d-flex flex-column col-12 col-md-3 mb-4">
                  <div
                    class="Top-Products-item d-flex align-items-center justify-content-between w-100 p-2"
                  >
                    <div class="Right-Top-Products-item d-flex align-items-center">
                      <div class="spacialAccount Pro-wrapper">
                        <img src="./assets/img/Pro.png" alt="" class="cursPointer" />
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
                    <img src="./assets/img/call-of-duty.png" alt="" class="w-100" />
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
                        <img src="./assets/img/windows.png" alt="" />
                      </div>
                      <div
                        class="left-bottom-bottom-Products-item d-flex flex-column"
                      >
                        <p class="m-0 p-0">تومان</p>
                        <p class="m-0 p-0 Price">15000000</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="Products-item d-flex flex-column col-12 col-md-3 mb-4">
                  <div
                    class="Top-Products-item d-flex align-items-center justify-content-between w-100 p-2"
                  >
                    <div class="Right-Top-Products-item d-flex align-items-center">
                      <div class="spacialAccount Pro-wrapper">
                        <img src="./assets/img/Pro.png" alt="" class="cursPointer" />
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
                    <img src="./assets/img/call-of-duty.png" alt="" class="w-100" />
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
                        <img src="./assets/img/windows.png" alt="" />
                      </div>
                      <div
                        class="left-bottom-bottom-Products-item d-flex flex-column"
                      >
                        <p class="m-0 p-0">تومان</p>
                        <p class="m-0 p-0 Price">15000000</p>
                      </div>
                    </div>
                  </div>
                </div> -->
            </div>

            <!-- Bottom navigation Bar -->
            <div
                class="d-flex d-md-none w-100 position-fixed bottom-0 bottomNavigation row align-items-center p-1 m-0 justify-content-around"
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
