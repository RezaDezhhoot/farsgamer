<!DOCTYPE html>
<html lang="en" xmlns:wire="http://www.w3.org/1999/xhtml">
@include('livewire.site.layouts.auth.head')
<body class="login">
<div>
    <a class="hiddenanchor" id="signup"></a>
    <a class="hiddenanchor" id="signin"></a>
    <a class="hiddenanchor" id="reset"></a>
    <div class="login_wrapper">
        @yield('content')
    </div>
</div>
@livewireScripts
</body>
</html>
