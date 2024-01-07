<div class="sidebar-inner mx-3">
    <ul class="nav flex-column mt-4">
        <li class="nav-item {{ request()->routeIs('targetforce.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('targetforce.dashboard') }}">
                <i class="fa-fw fas fa-home mr-2"></i><span>{{ __('Dashboard') }}</span>
            </a>
        </li>
        <li class="nav-item {{ request()->is('*posts*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('targetforce.posts.index') }}">
                <i class="fa-fw fas fa-envelope mr-2"></i><span>{{ __('Posts') }}</span>
            </a>
        </li>
        <li class="nav-item {{ request()->is('*froms*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('targetforce.forms') }}">
                <i class="fa-fw fas fa-envelope mr-2"></i><span>{{ __('Forms') }}</span>
            </a>
        </li>
        <li class="nav-item {{ request()->is('*notifications*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('targetforce.subscribers.index') }}">
                <i class="fa-fw fas fa-envelope mr-2"></i><span>{{ __('Notifications') }}</span>
            </a>
        </li>
        <li class="nav-item {{ request()->is('*subscribers*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('targetforce.subscribers.index') }}">
                <i class="fa-fw fas fa-user mr-2"></i><span>{{ __('Subscribers') }}</span>
            </a>
        </li>
        <li class="nav-item {{ request()->is('*messages*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('targetforce.messages.index') }}">
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
                        <a class="nav-link" href="{{ route('targetforce.email_services.index') }}">
                            <i class="fa-fw fas fa-envelope mr-2"></i><span>{{ __('Email Services') }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        

        {!! \Targetforce\Base\Facades\Targetforce::sidebarHtmlContent() !!}

    </ul>
</div>
