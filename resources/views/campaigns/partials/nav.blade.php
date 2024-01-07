<ul class="nav nav-pills mb-4">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('targetforce.campaigns.index') ? 'active'  : '' }}"
           href="{{ route('targetforce.campaigns.index') }}">{{ __('Draft') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('targetforce.campaigns.sent') ? 'active'  : '' }}"
           href="{{ route('targetforce.campaigns.sent') }}">{{ __('Sent') }}</a>
    </li>
</ul>
