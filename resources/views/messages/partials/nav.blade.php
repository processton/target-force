<ul class="nav nav-pills mb-4">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('targetforce.messages.index') ? 'active'  : '' }}"
           href="{{ route('targetforce.messages.index') }}">{{ __('Sent') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('targetforce.messages.draft') ? 'active'  : '' }}"
           href="{{ route('targetforce.messages.draft') }}">{{ __('Draft') }}</a>
    </li>
</ul>
