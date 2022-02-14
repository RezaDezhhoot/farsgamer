@props(['icon','label','active'])
<li class="menu-item {{$active  ? 'menu-item-active' : '' }}" aria-haspopup="true" data-menu-toggle="hover">
    <a {!! $attributes !!} class="menu-link">
        <i class="menu-icon {{$icon}}">
            <span></span>
        </i>
        <span class="menu-text">{{ $label }}</span>
    </a>
</li>
