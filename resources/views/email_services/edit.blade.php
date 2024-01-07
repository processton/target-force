@extends('targetforce::layouts.app')

@section('heading')
    {{ __('Email Services') }}
@stop

@section('content')

    @component('targetforce::layouts.partials.card')
        @slot('cardHeader', __('Edit Email Service'))

        @slot('cardBody')
            <form action="{{ route('targetforce.email_services.update', $emailService->id) }}" method="POST" class="form-horizontal">
                @csrf
                @method('PUT')
                <x-targetforce.text-field name="name" :label="__('Name')" :value="$emailService->name" />

                @include('targetforce::email_services.options.' . strtolower($emailServiceType->name), ['settings' => $emailService->settings])

                <x-targetforce.submit-button :label="__('Update')" />
            </form>
        @endSlot
    @endcomponent

@stop
