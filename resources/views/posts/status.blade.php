@extends('targetforce::layouts.app')

@section('title', __('Post Status'))

@section('heading')
    {{ __('Post Status') }}
@stop

@section('content')



<div class="card">
    <div class="card-header card-header-accent">
        <div class="card-header-inner">
            {{ __('Your post is currently') }} <strong>{{ strtolower($post->status->name) }}</strong>
        </div>
    </div>
    <div class="card-body">
        @if ($post->queued)
            Your post is queued and will be sent out soon.
        @elseif ($post->cancelled)
            Your post was cancelled.
        @else
            <i class="fas fa-cog fa-spin"></i>
            {{ $postStats[$post->id]['counts']['sent'] }} out of {{ $postStats[$post->id]['counts']['total'] }} messages sent.
        @endif
    </div>
</div>

@stop
