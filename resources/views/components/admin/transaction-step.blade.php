@props(['icon','label','active'=>'pending','desc'=>false])
<div class="wizard-step" data-wizard-type="step" data-wizard-state="{{$active}}">
    <div class="wizard-wrapper">
        <div class="wizard-icon">
            <span class="svg-icon svg-icon-2x">
                 <i class="{{$icon}}">
                    <span></span>
                </i>
            </span>

        </div>
        <div class="wizard-label">
            <h3 class="wizard-title">{{$label}}</h3>
            <div class="wizard-desc">{{$desc}}</div>
        </div>
    </div>
</div>
