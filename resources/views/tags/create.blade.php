@extends('targetforce::layouts.app')

@section('title', __('New Tag'))

@section('heading')
    {{ __('Tags') }}
@stop

@section('content')

    @component('targetforce::layouts.partials.card')
        @slot('cardHeader', __('Create Tag'))

        @slot('cardBody')
            <form action="{{ route('targetforce.tags.store') }}" method="POST" class="form-horizontal">
                @csrf

                @include('targetforce::tags.partials.form')

                <x-targetforce.submit-button :label="__('Save')" />
            </form>
        @endSlot
    @endcomponent

@stop
