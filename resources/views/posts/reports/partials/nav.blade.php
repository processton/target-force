<ul class="nav nav-pills mb-3">
    <li class="nav-item">
        <a class="nav-link {{ request()->route()->named('targetforce.posts.reports.index') ? 'active'  : '' }}"
           href="{{ route('targetforce.posts.reports.index', $post->id) }}">{{ __('Overview') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->route()->named('targetforce.posts.reports.recipients') ? 'active'  : '' }}"
           href="{{ route('targetforce.posts.reports.recipients', $post->id) }}">{{ __('Recipients') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->route()->named('targetforce.posts.reports.opens') ? 'active'  : '' }}"
           href="{{ route('targetforce.posts.reports.opens', $post->id) }}">{{ __('Opens') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->route()->named('targetforce.posts.reports.clicks') ? 'active'  : '' }}"
           href="{{ route('targetforce.posts.reports.clicks', $post->id) }}">{{ __('Clicks') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->route()->named('targetforce.posts.reports.bounces') ? 'active'  : '' }}"
           href="{{ route('targetforce.posts.reports.bounces', $post->id) }}">{{ __('Bounces') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->route()->named('targetforce.posts.reports.unsubscribes') ? 'active'  : '' }}"
           href="{{ route('targetforce.posts.reports.unsubscribes', $post->id) }}">{{ __('Unsubscribes') }}</a>
    </li>
</ul>
