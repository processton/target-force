<ul class="nav nav-pills mb-3">
    <li class="nav-item">
        <a class="nav-link {{ request()->route()->named('targetforce.campaigns.reports.index') ? 'active'  : '' }}"
           href="{{ route('targetforce.campaigns.reports.index', $campaign->id) }}">{{ __('Overview') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->route()->named('targetforce.campaigns.reports.recipients') ? 'active'  : '' }}"
           href="{{ route('targetforce.campaigns.reports.recipients', $campaign->id) }}">{{ __('Recipients') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->route()->named('targetforce.campaigns.reports.opens') ? 'active'  : '' }}"
           href="{{ route('targetforce.campaigns.reports.opens', $campaign->id) }}">{{ __('Opens') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->route()->named('targetforce.campaigns.reports.clicks') ? 'active'  : '' }}"
           href="{{ route('targetforce.campaigns.reports.clicks', $campaign->id) }}">{{ __('Clicks') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->route()->named('targetforce.campaigns.reports.bounces') ? 'active'  : '' }}"
           href="{{ route('targetforce.campaigns.reports.bounces', $campaign->id) }}">{{ __('Bounces') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->route()->named('targetforce.campaigns.reports.unsubscribes') ? 'active'  : '' }}"
           href="{{ route('targetforce.campaigns.reports.unsubscribes', $campaign->id) }}">{{ __('Unsubscribes') }}</a>
    </li>
</ul>
