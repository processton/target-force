@extends('targetforce::layouts.app')

@section('title', __('Campaigns'))

@section('heading')
    {{ __('Campaigns') }}
@endsection

@section('content')

    @include('targetforce::campaigns.partials.nav')

    @component('targetforce::layouts.partials.actions')
        @slot('right')
            <a class="btn btn-primary btn-md btn-flat" href="{{ route('targetforce.campaigns.create') }}">
                <i class="fa fa-plus mr-1"></i> {{ __('New Campaign') }}
            </a>
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-table table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('Name') }}</th>
                    @if (request()->routeIs('targetforce.campaigns.sent'))
                        <th>{{ __('Sent') }}</th>
                        <th>{{ __('Opened') }}</th>
                        <th>{{ __('Clicked') }}</th>
                    @endif
                    <th>{{ __('Created') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($campaigns as $campaign)
                    <tr>
                        <td>
                            @if ($campaign->draft)
                                <a href="{{ route('targetforce.campaigns.edit', $campaign->id) }}">{{ $campaign->name }}</a>
                            @elseif($campaign->sent)
                                <a href="{{ route('targetforce.campaigns.reports.index', $campaign->id) }}">{{ $campaign->name }}</a>
                            @else
                                <a href="{{ route('targetforce.campaigns.status', $campaign->id) }}">{{ $campaign->name }}</a>
                            @endif
                        </td>
                        @if (request()->routeIs('targetforce.campaigns.sent'))
                            <td>{{ $campaignStats[$campaign->id]['counts']['sent'] }}</td>
                            <td>{{ number_format($campaignStats[$campaign->id]['ratios']['open'] * 100, 1) . '%' }}</td>
                            <td>
                                {{ number_format($campaignStats[$campaign->id]['ratios']['click'] * 100, 1) . '%' }}
                            </td>
                        @endif
                        <td><span title="{{ $campaign->created_at }}">{{ $campaign->created_at->diffForHumans() }}</span></td>
                        <td>
                            @include('targetforce::campaigns.partials.status')
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm btn-wide" type="button" id="dropdownMenuButton"
                                        data-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    @if ($campaign->draft)
                                        <a href="{{ route('targetforce.campaigns.edit', $campaign->id) }}"
                                           class="dropdown-item">
                                            {{ __('Edit') }}
                                        </a>
                                    @else
                                        <a href="{{ route('targetforce.campaigns.reports.index', $campaign->id) }}"
                                           class="dropdown-item">
                                            {{ __('View Report') }}
                                        </a>
                                    @endif

                                    <a href="{{ route('targetforce.campaigns.duplicate', $campaign->id) }}"
                                       class="dropdown-item">
                                        {{ __('Duplicate') }}
                                    </a>

                                    @if($campaign->canBeCancelled())
                                        <div class="dropdown-divider"></div>
                                        <a href="{{ route('targetforce.campaigns.confirm-cancel', $campaign->id) }}"
                                           class="dropdown-item">
                                            {{ __('Cancel') }}
                                        </a>
                                    @endif

                                    @if ($campaign->draft)
                                        <div class="dropdown-divider"></div>
                                        <a href="{{ route('targetforce.campaigns.destroy.confirm', $campaign->id) }}"
                                           class="dropdown-item">
                                            {{ __('Delete') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%">
                            <p class="empty-table-text">
                                @if (request()->routeIs('targetforce.campaigns.index'))
                                    {{ __('You do not have any draft campaigns.') }}
                                @else
                                    {{ __('You do not have any sent campaigns.') }}
                                @endif
                            </p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @include('targetforce::layouts.partials.pagination', ['records' => $campaigns])

@endsection
