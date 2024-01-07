@extends('targetforce::layouts.app')

@section('title', __('Forms'))

@section('heading')
    {{ __('Forms') }}
@endsection

@section('content')
    <link href="{{ asset('vendor/targetforce/build/assets/app.css') }}" rel="stylesheet">
    @component('targetforce::layouts.partials.actions')
        @slot('right')
            <a class="btn btn-primary btn-md btn-flat" href="{{ route('targetforce.email_services.create') }}">
                <i class="fa fa-plus mr-1"></i> {{ __('Add Form') }}
            </a>
        @endslot
    @endcomponent

    <div id="app">
    </div>
    
    
    <script type="module" src="{{ asset('vendor/targetforce/build/assets/app.js') }}"></script>
@endsection
