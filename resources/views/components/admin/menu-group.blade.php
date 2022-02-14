@props(['icon','label','active'])
<li class="menu-item menu-item-submenu {{$active  ? 'menu-item-open' : '' }}" aria-haspopup="true" data-menu-toggle="hover">
    <a href="javascript:;" class="menu-link menu-toggle">
        <i class="menu-icon {{$icon}}"></i>
        <span class="menu-text">{{$label}}</span>
        <i class="menu-arrow"></i>
    </a>
    <div class="menu-submenu" style="{{$active ? '' : 'display: none; overflow: hidden;'}}">
        <i class="menu-arrow"></i>
        <ul class="menu-subnav">
            <li class="menu-item menu-item-parent " aria-haspopup="true">
                <span class="menu-link">
                    <span class="menu-text">{{$label}}</span>
                </span>
            </li>
            {{ $slot }}
        </ul>
    </div>
</li>
