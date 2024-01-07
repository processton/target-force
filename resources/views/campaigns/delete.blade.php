@extends('targetforce::layouts.app')

@section('title', __('Delete Post'))

@section('heading')
    @lang('Delete Post') - {{ $post->name }}
@endsection

@section('content')

    @component('targetforce::layouts.partials.actions')
        @slot('right')
            <a class="btn btn-primary btn-md btn-flat" href="{{ route('targetforce.posts.create') }}">
                <i class="fa fa-plus mr-1"></i> {{ __('Create Post') }}
            </a>
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-header card-header-accent">
            <div class="card-header-inner">
                {{ __('Confirm Delete') }}
            </div>
        </div>
        <div class="card-body">
            <p>
                {!! __('Are you sure that you want to delete the <b>:name</b> post?', ['name' => $post->name]) !!}
            </p>
            <form action="{{ route('targetforce.posts.destroy', $post->id) }}" method="post">
                @csrf
                @method('DELETE')
                <input type="hidden" name="id" value="{{ $post->id }}">
                <a href="{{ route('targetforce.posts.index') }}" class="btn btn-md btn-light">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-md btn-danger">{{ __('DELETE') }}</button>
            </form>
        </div>
    </div>

@endsection
