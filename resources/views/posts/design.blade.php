@extends('targetforce::layouts.app')

@section('title', __('Post Design'))

@section('heading')
    {{ __('Post Design') }}
@stop

@section('content')

    <form action="{{ route('posts.content.update', $post->id) }}" method="POST">
        @csrf
        @method('PUT')

        @include('targetforce::templates.partials.editor')

        <br>

        <a href="{{ route('targetforce.posts.template', $post->id) }}" class="btn btn-link"><i
                class="fa fa-arrow-left"></i> {{ __('Back') }}</a>

        <button class="btn btn-primary" type="submit">{{ __('Save and continue') }}</button>
    </form>
@stop
