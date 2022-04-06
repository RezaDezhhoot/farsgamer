@foreach($form as $key => $item)
    @if($transaction->is_returned)
        @if($item['status'] == 'normal')
            @continue
        @endif
            @if($transaction->status == $sendingData && $item['for'] == 'customer')
                @continue
            @elseif($transaction->status == $send && $item['for'] == 'seller')
                @continue
            @endif
    @else
        @if($item['status'] == 'return')
            @continue
        @endif
            @if($transaction->status == $send && $item['for'] == 'customer')
                @continue
            @elseif($transaction->status == $pay && $item['for'] == 'seller')
                @continue
            @endif
    @endif
    @if($item['type'] == 'text')
        <div class="form-group">
            <div class="col-span-2 lg:col-span-{{$item['width']}}">
                <label for="{{$key}}"> {!! $item['label'] !!}</label>
                <input id="{{$key}}" type="text" name="{{$item['name']}}" class="form-control text-field" placeholder="{{$item['placeholder']}}"
                       wire:model.defer="transactionData.{{$item['name']}}">
                @error('transactionData.'.$item['name'].'.error')
                <small class="text-danger">{{$message}}</small>
                @enderror
                <p>
                    مخاطب : {{ $data['for'][$item['for']] }}
                </p>
            </div>
        </div>
        <hr>
    @elseif($item['type'] == 'textArea')
        <div class="form-group">
            <div class="col-span-2 lg:col-span-{{$item['width']}}">
                <label for="{{$key}}" class="account-email">{!! $item['label'] !!}</label>
                <textarea id="{{$key}}" name="{{$item['name']}}" class="form-control text-field h-auto resize-y"
                          placeholder="{{$item['placeholder']}}" rows="4" wire:model.defer="transactionData.{{$item['name']}}"></textarea>
                @error('transactionData.'.$item['name'].'.error')
                <small class="text-danger">{{$message}}</small>
                @enderror
                <p>
                    مخاطب : {{ $data['for'][$item['for']] }}
                </p>
            </div>
        </div>
        <hr>
    @elseif($item['type'] == 'select')
        <div class="form-group" >
            <div class="col-span-2 lg:col-span-{{$item['width']}}">
                <label for="{{$key}}" class="account-email">{!! $item['label'] !!}</label>
                <select id="{{$key}}" name="{{$item['name']}}" class="form-control text-field h-auto resize-y" wire:model.defer="transactionData.{{$item['name']}}">
                    <option value="">انتخاب کنید...</option>
                    @foreach($item['options'] as $option)
                        <option value="{{$option['value']}}">{{$option['name']}}</option>
                    @endforeach
                </select>
                @error('transactionData.'.$item['name'].'.error')
                <small class="text-danger">{{$message}}</small>
                @enderror
                <p>
                    مخاطب : {{ $data['for'][$item['for']] }}
                </p>
            </div>
        </div>
        <hr>
{{--    @elseif($item['type'] == 'customRadio')--}}
{{--        <div class="form-group">--}}
{{--            <div class="col-{{$item['width']}}">--}}
{{--                <label class="mb-2">{!! $item['label'] !!}</label>--}}
{{--                <div class="d-flex row">--}}
{{--                    @foreach($item['options'] as $keyRadio => $radio)--}}
{{--                        <div class="justify-center align-items-center col-3 p-2" dir="ltr">--}}
{{--                            <label for="{{$key}}-{{$keyRadio}}" class="account-category-btn">{{$radio['name']}}--}}
{{--                                <input id="{{$key}}-{{$keyRadio}}" class="" type="radio" value="{{ $radio['value'] }}" name="{{$item['name']}}"--}}
{{--                                       wire:model.defer="transactionData.{{$item['name']}}">--}}
{{--                            </label>--}}
{{--                        </div>--}}
{{--                    @endforeach--}}
{{--                </div>--}}
{{--                @error('transactionData.'.$item['name'].'.error')--}}
{{--                <small class="text-danger">{{$message}}</small>--}}
{{--                @enderror--}}
{{--                <p>--}}
{{--                    مخاطب : {{ $data['for'][$item['for']] }}--}}
{{--                </p>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <hr>--}}
    @endif
@endforeach
