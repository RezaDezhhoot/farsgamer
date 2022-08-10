<div>
    <!--begin::Login Header-->
    <div class="d-flex flex-center mb-15">
        <a>
            <img src="{{$logo}}" class="max-h-75px" alt="" />
        </a>
    </div>
    <!--end::Login Header-->
    <!--begin::Login Sign in form-->
    <div class="login-signin">
        <div class="mb-20">
            <h3>ورود به ادمین</h3>
        </div>
        <form class="form" wire:submit.prevent="login" method="post" id="kt_login_signin_form">
            <div class="form-group mb-5">
                <input wire:model.defer="phone" style="text-align: right" class="form-control h-auto form-control-solid py-4 px-8" type="text" placeholder="موبایل" name="username" autocomplete="off" />
                @error('phone')
                <span class="text-danger">
                                    {{ $message }}
                                </span>
                @enderror
            </div>
            <div class="form-group mb-5">
                <input wire:model.defer="password" style="text-align: right" class="form-control h-auto form-control-solid py-4 px-8" type="password" placeholder="پسورد" name="password" />
                @error('password')
                <span class="text-danger">
                                    {{ $message }}
                                </span>
                @enderror
                <div class="form-group d-flex flex-wrap justify-content-between align-items-center">
                    @if(!$sent)
                    <a style="cursor: pointer" wire:click="sendSMS()" id="kt_login_forgot" class="text-muted text-hover-primary">ارسال رمز یکبار مصرف</a>
                    @else
                        <a  class="text-muted text-success">ارسال شد</a>
                    @endif
                </div>
            </div>
            <button type="submit" id="kt_login_signin_submit" class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-4">ورود</button>
        </form>
    </div>
    <!--end::Login Sign in form-->

</div>
