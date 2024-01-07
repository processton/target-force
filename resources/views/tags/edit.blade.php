@extends('targetforce::layouts.app')

@section('title', __("Edit Tag"))

@section('heading')
    {{ __('Tags') }}
@stop

@section('content')

    @component('targetforce::layouts.partials.card')
        @slot('cardHeader', __('Edit Tag'))

        @slot('cardBody')
            <form action="{{ route('targetforce.tags.update', $tag->id) }}" method="POST" class="form-horizontal">
                @csrf
                @method('PUT')

                @include('targetforce::tags.partials.form')

                <x-targetforce.submit-button :label="__('Save')" />
            </form>
        @endSlot
    @endcomponent

@stop
