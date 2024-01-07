@extends('common.template')

@section('heading')
    {{ __('Post') }}: {{ $post->name }}
@stop

@section('content')

    @if ($post->content ?? false)
        <a href="{{ route('targetforce.posts.preview', $post->id) }}">
            {{ __('Confirm and Send Post') }}
        </a>
    @else
        <ul>
            <li><a href="{{ route('targetforce.posts.edit', $post->id) }}">{{ __('Edit Post') }}</a></li>
            <li>
                <a href="{{ route('targetforce.posts.create', ['id' => $post->id]) }}">{{ __('Create Email') }}</a>
            </li>
        </ul>
    @endif

@stop
