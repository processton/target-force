@extends('targetforce::layouts.app')

@section('title', __('Create Campaign'))

@section('heading', __('Campaigns'))

@section('content')

    @if( ! $emailServices)
        <div class="callout callout-danger">
            <h4>{{ __('You haven\'t added any email service!') }}</h4>
            <p>{{ __('Before you can create a campaign, you must first') }} <a
                    href="{{ route('targetforce.email_services.create') }}">{{ __('add an email service') }}</a>.
            </p>
        </div>
    @else
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card">
                    <div class="card-header">
                        {{ __('Create Campaign') }}
                    </div>
                    <div class="card-body">
                        <form action="{{ route('targetforce.campaigns.store') }}" method="POST" class="form-horizontal">
                            @csrf
                            @include('targetforce::campaigns.partials.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
	@endif
@stop
