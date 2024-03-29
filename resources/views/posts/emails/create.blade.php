@extends('targetforce::layouts.app')

@section('title', __('Create Email'))

@section('heading')
    {{ __('Create Email') }}
@stop

@section('content')

    {!! Form::open(['route' => ['steps', $post->id], 'class' => 'form-horizontal']) !!}

    @include('emails.partials.form')

    {!! Form::submitButton(__('Create')) !!}
    {!! Form::close() !!}

@stop
