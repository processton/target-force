@extends('targetforce::layouts.app')

@section('title', __("Edit Subscriber") . " : {$subscriber->full_name}")

@section('heading')
    {{ __('Subscribers') }}
@stop

@section('content')

    @component('targetforce::layouts.partials.card')
        @slot('cardHeader', __('Edit Subscriber'))

        @slot('cardBody')
            <form action="{{ route('targetforce.subscribers.update', $subscriber->id) }}" method="POST" class="form-horizontal">
                @csrf
                @method('PUT')

                @include('targetforce::subscribers.partials.form')

                <x-targetforce.submit-button :label="__('Save')" />
            </form>
        @endSlot
    @endcomponent

@stop
