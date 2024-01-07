<ul class="nav nav-pills mb-4">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('targetforce.posts.index') ? 'active'  : '' }}"
           href="{{ route('targetforce.posts.index') }}">{{ __('Draft') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('targetforce.posts.sent') ? 'active'  : '' }}"
           href="{{ route('targetforce.posts.sent') }}">{{ __('Sent') }}</a>
    </li>
</ul>
