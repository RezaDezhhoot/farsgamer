<div>
    <!-- Start Footer Area -->
    <footer class="footer-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="single-footer-widget mb-30">
                        <h3>تماس بگیرید</h3>

                        <ul class="contact-us-link">
                            <li>
                                <i class='bx bx-map'></i>
                                <a  target="_blank">{{$data['address']}}</a>
                            </li>
                            <li>
                                <i class='bx bx-phone-call'></i>
                                <a href="tel:{{$data['tel']}}">{{$data['tel']}}</a>
                            </li>
                            <li>
                                <i class='bx bx-envelope'></i>
                                <a href="mailto:{{$data['email']}}">{{$data['email']}}</a>
                            </li>
                        </ul>

                        <ul class="social-link">
                            @foreach($data['contact'] as $item)
                                <li><a href="{{ $item['link'] }}" class="d-block" target="_blank"><i class='{{ $item['img'] }}'></i></a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="single-footer-widget mb-30">
                        <h3>برگه ها</h3>

                        <ul class="support-link">
                            <li><a href="{{ route('auth') }}">پنل کاربری</a></li>
                            <li><a href="{{ route('categories') }}">اخبار</a></li>
                            <li><a href="{{ route('law') }}">مقررات</a></li>
                            <li><a href="{{ route('contactUs') }}">ارتباط با ما</a></li>
                            <li><a href="{{ route('aboutUs') }}">درباره ما</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="single-footer-widget mb-30">
                        <h3>لینکهای مفید</h3>

                        <ul class="useful-link">
                            <li><a href="{{ route('courses') }}">دوره های اموزشی</a></li>
                            <li><a href="{{ route('categories') }}">گروه های اموزشی</a></li>
                            <li><a href="{{ route('organizations') }}">سازمان ها و ادارات</a></li>
                            <li><a href="{{ route('documents') }}">مدارک و گواینامه ها</a></li>
                            <li><a href="#">مشاوره آنلاین </a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="single-footer-widget mb-30">
                        <h3>خبرنامه</h3>

                        <div class="newsletter-box">
                            <p>برای دریافت آخرین اخبار و آخرین به روزرسانی های ما</p>

                            <form class="newsletter-form" data-toggle="validator">
                                <label>ایمیل شما:</label>
                                <input type="email" class="input-newsletter" wire:model.defer="email" placeholder="ایمیل خود را وارد کنید" name="EMAIL" required autocomplete="off">
                                <button type="button" wire:click="registerEmail()" >مشترک شدن</button>
                                <div id="validator-newsletter" class="form-result">
                                    @error('email')
                                        {{ $message  }}
                                    @enderror
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom-area">
            <div class="container">
                <div class="logo">
                    <a href="{{route('home')}}" class="d-inline-block"><img src="{{ asset($data['logo']) }}" alt="image"></a>
                </div>
                <p>{{$data['copyRight']}} <i class='bx bx-copyright'></i> </p>
            </div>
        </div>
    </footer>
    <!-- End Footer Area -->

    <div class="go-top"><i class='bx bx-up-arrow-alt'></i></div>

</div>
