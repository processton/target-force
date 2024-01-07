@extends('targetforce::layouts.app')

@section('heading')
    {{ __('Test Email Service') }}
@stop

@section('content')

    @component('targetforce::layouts.partials.card')
        @slot('cardHeader', __('Test Email Service') . ' : ' . $emailService->name)

        @slot('cardBody')
            <form action="{{ route('targetforce.email_services.test.store', $emailService->id) }}" method="POST" class="form-horizontal">
                @csrf

                <x-targetforce.text-field name="to" :label="__('To Email')" />

                <div class="form-group row form-group-email">
                    <label for="id-field-email" class="control-label col-sm-3">{{ __('From Email') }}</label>
                    <div class="col-sm-9">
                        <input id="id-field-email" class="form-control" name="from" type="email" required>
                        <small class="form-text text-muted">{{ __('Must be a verified :service email address or domain', ['service' => $emailService->type->name]) }}</small>
                    </div>
                </div>

                <x-targetforce.text-field name="subject" :label="__('Subject')" value="Targetforce Test Email" required="required" />

                <x-targetforce.textarea-field name="body" :label="__('Email Body')" required="required" rows="5">This is a test for the email service {{ $emailService->name }}</x-targetforce.textarea-field>

                <x-targetforce.submit-button :label="__('Test')" />
            </form>
        @endSlot
    @endcomponent

@stop


