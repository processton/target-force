@extends('targetforce::layouts.app')

@section('title', __('New Subscriber'))

@section('heading')
    {{ __('Subscribers') }}
@stop

@section('content')

    @component('targetforce::layouts.partials.card')
        @slot('cardHeader', __('Create Subscriber'))

        @slot('cardBody')
            <form action="{{ route('targetforce.subscribers.store') }}" class="form-horizontal" method="POST">
                @csrf
                @include('targetforce::subscribers.partials.form')

                <x-targetforce.submit-button :label="__('Save')" />
            </form>
        @endSlot
    @endcomponent

@stop
