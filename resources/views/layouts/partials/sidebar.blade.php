<div class="sidebar-inner mx-3">
    <ul class="nav flex-column mt-4">
        <li class="nav-item {{ request()->routeIs('sendportal.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('sendportal.dashboard') }}">
                <i class="fa-fw fas fa-home mr-2"></i><span>{{ __('Dashboard') }}</span>
            </a>
        </li>
        <li class="nav-item {{ request()->is('*campaigns*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('sendportal.campaigns.index') }}">
                <i class="fa-fw fas fa-envelope mr-2"></i><span>{{ __('Campaigns') }}</span>
            </a>
        </li>
        <li class="nav-item {{ request()->is('*froms*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('sendportal.forms') }}">
                <i class="fa-fw fas fa-envelope mr-2"></i><span>{{ __('Forms') }}</span>
            </a>
        </li>
        <li class="nav-item {{ request()->is('*notifications*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('sendportal.subscribers.index') }}">
                <i class="fa-fw fas fa-envelope mr-2"></i><span>{{ __('Notifications') }}</span>
            </a>
        </li>
        <li class="nav-item {{ request()->is('*subscribers*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('sendportal.subscribers.index') }}">
                <i class="fa-fw fas fa-user mr-2"></i><span>{{ __('Subscribers') }}</span>
            </a>
        </li>
        <li class="nav-item {{ request()->is('*messages*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('sendportal.messages.index') }}">
                <i class="fa-fw fas fa-paper-plane mr-2"></i><span>{{ __('Messages') }}</span>
            </a>
        </li>
        <li class="sidebar-dropdown">
            <a class="sidebar-link" onclick="document.getElementById('settings-dropdown').classList.toggle('active');">
                <i class="fa fa-tachometer-alt"></i>
                <span>{{ __('Settings') }}</span>
            </a>
            <div class="sidebar-submenu" id="settings-dropdown">
                <ul>
                    <li class="nav-item {{ request()->is('*email-services*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('sendportal.email_services.index') }}">
                            <i class="fa-fw fas fa-envelope mr-2"></i><span>{{ __('Email Services') }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        

        {!! \Sendportal\Base\Facades\Sendportal::sidebarHtmlContent() !!}

    </ul>
</div>
