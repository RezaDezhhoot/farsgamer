@props(['id', 'label', 'help', 'required' => false])
<div class="form-group col-12 d-flex align-items-center m-0">
    <label for="{{$id}}"> {{$required ? '*' : ''}}</label>
    {{$label}} <input id="{{$id}}" {!! $attributes->merge(['class'=> 'form-control p-datepicker']) !!}
    x-data
           x-init="$('#{{$id}}').pDatepicker({
                initialValue: false,
                autoClose: true,
                calendarType: 'gregorian',
                calendar: {
                    'persian': {
                    'locale': 'en',
                    'showHint': true,
                    'leapYearMode': 'algorithmic'
                    },
                    'gregorian': {
                        'locale': 'en',
                        'showHint': true
                    },
                },
                toolbox: {
                    'enabled': true,
                    'calendarSwitch': {
                    'enabled': false,
                    },
                },
                format: 'YYYY-MM-DD H:m',
                onSelect: function () {
                    $dispatch('input', $('#{{$id}}').val())
                }
                });">
    @isset($help)
        <small class="text-muted">{{$help}}</small>
    @endisset
</div>
