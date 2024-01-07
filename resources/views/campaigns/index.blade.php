@extends('targetforce::layouts.app')

@section('title', __('Posts'))

@section('heading')
    {{ __('Posts') }}
@endsection

@section('content')

    @include('targetforce::posts.partials.nav')

    @component('targetforce::layouts.partials.actions')
        @slot('right')
            <a class="btn btn-primary btn-md btn-flat" href="{{ route('targetforce.posts.create') }}">
                <i class="fa fa-plus mr-1"></i> {{ __('New Post') }}
            </a>
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-table table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('Name') }}</th>
                    @if (request()->routeIs('targetforce.posts.sent'))
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
                @forelse($posts as $post)
                    <tr>
                        <td>
                            @if ($post->draft)
                                <a href="{{ route('targetforce.posts.edit', $post->id) }}">{{ $post->name }}</a>
                            @elseif($post->sent)
                                <a href="{{ route('targetforce.posts.reports.index', $post->id) }}">{{ $post->name }}</a>
                            @else
                                <a href="{{ route('targetforce.posts.status', $post->id) }}">{{ $post->name }}</a>
                            @endif
                        </td>
                        @if (request()->routeIs('targetforce.posts.sent'))
                            <td>{{ $postStats[$post->id]['counts']['sent'] }}</td>
                            <td>{{ number_format($postStats[$post->id]['ratios']['open'] * 100, 1) . '%' }}</td>
                            <td>
                                {{ number_format($postStats[$post->id]['ratios']['click'] * 100, 1) . '%' }}
                            </td>
                        @endif
                        <td><span title="{{ $post->created_at }}">{{ $post->created_at->diffForHumans() }}</span></td>
                        <td>
                            @include('targetforce::posts.partials.status')
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm btn-wide" type="button" id="dropdownMenuButton"
                                        data-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    @if ($post->draft)
                                        <a href="{{ route('targetforce.posts.edit', $post->id) }}"
                                           class="dropdown-item">
                                            {{ __('Edit') }}
                                        </a>
                                    @else
                                        <a href="{{ route('targetforce.posts.reports.index', $post->id) }}"
                                           class="dropdown-item">
                                            {{ __('View Report') }}
                                        </a>
                                    @endif

                                    <a href="{{ route('targetforce.posts.duplicate', $post->id) }}"
                                       class="dropdown-item">
                                        {{ __('Duplicate') }}
                                    </a>

                                    @if($post->canBeCancelled())
                                        <div class="dropdown-divider"></div>
                                        <a href="{{ route('targetforce.posts.confirm-cancel', $post->id) }}"
                                           class="dropdown-item">
                                            {{ __('Cancel') }}
                                        </a>
                                    @endif

                                    @if ($post->draft)
                                        <div class="dropdown-divider"></div>
                                        <a href="{{ route('targetforce.posts.destroy.confirm', $post->id) }}"
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
                                @if (request()->routeIs('targetforce.posts.index'))
                                    {{ __('You do not have any draft posts.') }}
                                @else
                                    {{ __('You do not have any sent posts.') }}
                                @endif
                            </p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @include('targetforce::layouts.partials.pagination', ['records' => $posts])

@endsection
