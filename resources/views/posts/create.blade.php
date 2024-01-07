@extends('targetforce::layouts.app')

@section('title', __('Create Post'))

@section('heading', __('Posts'))

@section('content')

    @if( ! $emailServices)
        <div class="callout callout-danger">
            <h4>{{ __('You haven\'t added any email service!') }}</h4>
            <p>{{ __('Before you can create a post, you must first') }} <a
                    href="{{ route('targetforce.email_services.create') }}">{{ __('add an email service') }}</a>.
            </p>
        </div>
    @else
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card">
                    <div class="card-header">
                        {{ __('Create Post') }}
                    </div>
                    <div class="card-body">
                        <form action="{{ route('targetforce.posts.store') }}" method="POST" class="form-horizontal">
                            @csrf
                            @include('targetforce::posts.partials.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
	@endif
@stop
