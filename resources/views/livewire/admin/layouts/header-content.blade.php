<div>
    <div class="page-title">
        <div class="title_left">
            <h3>{{$header}}
                <small>{{$headerDescriptions}}</small>
            </h3>
        </div>

        <div class="title_right">
            <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                <div class="input-group">
                    @if($mode == 'searchable')
                        <input wire:model="search" type="text" class="form-control" placeholder="جست و جو برای...">
                        <span class="input-group-btn">
                        <button class="btn btn-default" type="button">برو!</button>
                    @endif
                </span>
                </div>
            </div>
        </div>
    </div>

</div>
