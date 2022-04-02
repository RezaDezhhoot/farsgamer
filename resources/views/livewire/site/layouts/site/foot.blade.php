@livewireScripts
<script src="{{ asset('site/scripts/bootstrap.min.js') }}"></script>
<script src="{{ asset('site/scripts/main.global.js') }}"></script>
<script src="{{asset('bower_components/jquery.countdown/dist/jquery.countdown.js')}}"></script>
<script src="{{asset('site/library/swiper/swiper.min.js?v=1.0.1')}}"></script>


<script>
    Livewire.on('notify', data => {
        Swal.fire({
            position: 'top-end',
            icon: data.icon,
            title: data.title,
            showConfirmButton: false,
            timer: 4000,
            toast: true,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })
    })
</script>
@stack('scripts')
