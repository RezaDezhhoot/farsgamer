<div xmlns:wire="http://www.w3.org/1999/xhtml">
    <div class="animate form login_form">
        <section class="login_content">
            <form>
                <h1>فرم ورود</h1>
                <div>
                    <input type="text" class="form-control" placeholder="نام کاربری" required="" wire:model.defer="phone" />
                </div>
                <div>
                    <input type="password" class="form-control" placeholder="رمز ورود" required="" wire:model.defer="password" />
                </div>
                <div>
                    <a class="btn btn-default submit" wire:click="login()">ورود</a>
                    <a class="reset_pass" href="#reset">رمز ورود را از دست دادید؟</a>
                </div>

                <div class="clearfix"></div>

                <div class="separator">
                    <p class="change_link">
                        <a href="#signup" class="to_register">  ایجاد حساب  جدید در سایت؟  </a>
                    </p>
                </div>
            </form>
        </section>
    </div>
</div>
