@extends('targetforce::layouts.app')

@section('title', $post->name)

@section('heading', $post->name)

@section('content')

    @include('targetforce::posts.reports.partials.nav')

    <div class="row mb-4">
        <div class="col-md-4 col-sm-6 mb-md-0 mb-3">
            <div class="widget flex-row align-items-center align-items-stretch">
                <div class="col-8 py-4 rounded-right">
                    <div class="h4 m-0">{{ $post->unique_click_count }}</div>
                    <div class="text-uppercase">{{ __('Unique Clicks') }}</div>
                </div>
                <div class="col-4 d-flex align-items-center justify-content-center rounded-left">
                    <em class="far fa-hand-pointer fa-2x color-gray-400"></em>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6 mb-md-0 mb-3">
            <div class="widget flex-row align-items-center align-items-stretch">
                <div class="col-8 py-4 rounded-right">
                    <div class="h4 m-0">{{ $post->total_click_count }}</div>
                    <div class="text-uppercase">{{ __('Total Clicks') }}</div>
                </div>
                <div class="col-4 d-flex align-items-center justify-content-center rounded-left">
                    <em class="fas fa-hand-pointer fa-2x color-gray-400"></em>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6 mb-md-0 mb-3">
            <div class="widget flex-row align-items-center align-items-stretch">
                <div class="col-8 py-4 rounded-right">
                    <div class="h4 m-0">{{ $averageTimeToClick }}</div>
                    <div class="text-uppercase">{{ __('Avg. Time To Click') }}</div>
                </div>
                <div class="col-4 d-flex align-items-center justify-content-center rounded-left">
                    <em class="far fa-clock fa-2x color-gray-400"></em>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-table table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('Subscriber') }}</th>
                    <th>{{ __('Subject') }}</th>
                    <th>{{ __('Clicked') }}</th>
                    <th>{{ __('Click Count') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($messages as $message)
                    <tr>
                        <td>
                            <a href="{{ route('targetforce.subscribers.show', $message->subscriber_id) }}">{{ $message->recipient_email }}</a>
                        </td>
                        <td>{{ $message->subject }}</td>
                        <td>{{ \Targetforce\Base\Facades\Helper::displayDate($message->clicked_at) }}</td>
                        <td>{{ $message->click_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%">
                            <p class="empty-table-text">{{ __('There are no messages') }}</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @include('targetforce::layouts.partials.pagination', ['records' => $messages])

@endsection
