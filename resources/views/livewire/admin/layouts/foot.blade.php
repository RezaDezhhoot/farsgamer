<script>
    var KTAppSettings = {
        "breakpoints": {
            "sm": 576,
            "md": 768,
            "lg": 992,
            "xl": 1200,
            "xxl": 1400
        },
        "colors": {
            "theme": {
                "base": {
                    "white": "#ffffff",
                    "primary": "#3699FF",
                    "secondary": "#E5EAEE",
                    "success": "#1BC5BD",
                    "info": "#8950FC",
                    "warning": "#FFA800",
                    "danger": "#F64E60",
                    "light": "#E4E6EF",
                    "dark": "#181C32"
                },
                "light": {
                    "white": "#ffffff",
                    "primary": "#E1F0FF",
                    "secondary": "#EBEDF3",
                    "success": "#C9F7F5",
                    "info": "#EEE5FF",
                    "warning": "#FFF4DE",
                    "danger": "#FFE2E5",
                    "light": "#F3F6F9",
                    "dark": "#D6D6E0"
                },
                "inverse": {
                    "white": "#ffffff",
                    "primary": "#ffffff",
                    "secondary": "#3F4254",
                    "success": "#ffffff",
                    "info": "#ffffff",
                    "warning": "#ffffff",
                    "danger": "#ffffff",
                    "light": "#464E5F",
                    "dark": "#ffffff"
                }
            },
            "gray": {
                "gray-100": "#F3F6F9",
                "gray-200": "#EBEDF3",
                "gray-300": "#E4E6EF",
                "gray-400": "#D1D3E0",
                "gray-500": "#B5B5C3",
                "gray-600": "#7E8299",
                "gray-700": "#5E6278",
                "gray-800": "#3F4254",
                "gray-900": "#181C32"
            }
        },
        "font-family": "Poppins"
    };
</script>
<script src="{{asset('admin/plugins/global/plugins.bundle.js?v=7.0.6')}}"></script>
<script src="{{asset('admin/plugins/custom/prismjs/prismjs.bundle.js?v=7.0.6')}}"></script>
<script src="{{asset('admin/js/scripts.bundle.js?v=7.0.6')}}"></script>
{{--<script src="https://keenthemes.com/metronic/assets/js/engage_code.js"></script>--}}
<script src="{{asset('admin/plugins/custom/fullcalendar/fullcalendar.bundle.js')}}"></script>
<script src="{{asset('admin/js/pages/custom/chat/chat.js') }}"></script>
<script src="{{ asset('vendor/laravel-filemanager/js/stand-alone-button.js') }}"></script>
<script src="{{asset('admin/plugins/custom/datepicker/persian-date.min.js')}}"></script>
<script src="{{asset('admin/plugins/custom/datepicker/persian-datepicker.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{asset('/vendor/laravel-filemanager/js/stand-alone-button.js')}}"></script>
<script src="{{asset('bower_components/jquery.countdown/dist/jquery.countdown.js')}}"></script>
<script src="{{asset('admin/js/pages/widgets.js') }}"></script>
<script src="{{asset('admin/js/pages/crud/forms/widgets/bootstrap-datepicker.js') }}"></script>
{{--<script src="{{asset('admin/js/pages/custom/wizard/wizard-2.js') }}"></script>--}}
@livewireScripts


<script>
    Livewire.on('showModal', function (data) {
        const id = '#' + data + 'Modal';
        $(id).modal('show');
    })

    Livewire.on('hideModal', function (data) {
        const id = '#' + data + 'Modal';
        $(id).modal('hide');

    })

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


    Livewire.on('runChart', function (data) {
        var _initChartsWidget4 = function () {
            var element = document.getElementById("kt_charts_widget_4_chart2");
            if (!element) {
                return;
            }
            var obj = Object.values(data);
            var options = {
                series: [{
                    name: 'پرداختی ها',
                    data: obj[0]
                }, {
                    name: 'درخواست شده',
                    data: obj[3]
                },{
                    name: 'تسویه شده',
                    data: obj[2]
                }
                ],
                chart: {
                    type: 'line',
                    height: 350,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {},
                legend: {
                    show: false
                },
                dataLabels: {
                    enabled: false
                },
                fill: {
                    type: 'solid',
                    opacity: 1
                },
                stroke: {
                    curve: 'smooth'
                },
                xaxis: {
                    categories: obj[1],
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: KTApp.getSettings()['colors']['gray']['gray-500'],
                            fontSize: '12px',
                            fontFamily: KTApp.getSettings()['font-family']
                        }
                    },
                    crosshairs: {
                        position: 'front',
                        stroke: {
                            color: KTApp.getSettings()['colors']['theme']['light']['success'],
                            width: 1,
                            dashArray: 3
                        }
                    },
                    tooltip: {
                        enabled: false,
                        formatter: undefined,
                        offsetY: 0,
                        style: {
                            fontSize: '12px',
                            fontFamily: KTApp.getSettings()['font-family']
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: KTApp.getSettings()['colors']['gray']['gray-500'],
                            fontSize: '12px',
                            fontFamily: KTApp.getSettings()['font-family']
                        }
                    }
                },
                states: {
                    normal: {
                        filter: {
                            type: 'none',
                            value: 0
                        }
                    },
                    hover: {
                        filter: {
                            type: 'none',
                            value: 0
                        }
                    },
                    active: {
                        allowMultipleDataPointsSelection: false,
                        filter: {
                            type: 'none',
                            value: 0
                        }
                    }
                },
                tooltip: {
                    style: {
                        fontSize: '12px',
                        fontFamily: KTApp.getSettings()['font-family']
                    },
                    y: {
                        formatter: function (val) {
                            return  val + " تومان"
                        }
                    }
                },
                colors: [
                    KTApp.getSettings()['colors']['theme']['base']['success'],
                    KTApp.getSettings()['colors']['theme']['base']['danger'],
                    KTApp.getSettings()['colors']['theme']['base']['warning']
                ],
                grid: {
                    borderColor: KTApp.getSettings()['colors']['gray']['gray-200'],
                    strokeDashArray: 4,
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                markers: {
                    colors: [
                        KTApp.getSettings()['colors']['theme']['light']['success'],
                        KTApp.getSettings()['colors']['theme']['light']['danger'],
                        KTApp.getSettings()['colors']['theme']['light']['warning']
                    ],
                    strokeColor: [
                        KTApp.getSettings()['colors']['theme']['light']['success'],
                        KTApp.getSettings()['colors']['theme']['light']['danger'],
                        KTApp.getSettings()['colors']['theme']['light']['warning']
                    ],
                    strokeWidth: 3
                }
            };

            var chart = new ApexCharts(element, options);
            chart.render();
        }
        _initChartsWidget4();
    })
    $.fn.modal.Constructor.prototype.enforceFocus = function() {
        modal_this = this
        $(document).on('focusin.modal', function (e) {
            if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length
                && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_select')
                && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_text')) {
                modal_this.$element.focus()
            }
        })
    };
</script>
<script src="https://file-manager.webmai.ru/vendor/file-manager/js/file-manager.js"></script>
@stack('scripts')
