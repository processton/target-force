@extends('targetforce::layouts.app')

@section('heading')
    {{ __('Edit Email Content For Post') }} {{ $email->mailable->name }}
@stop

@section('content')

    {!! Form::open(['route' => ['steps', $email->mailable->id], 'method' => 'PUT', 'class' => 'form-horizontal']) !!}

    @include('emails.content.partials.form')

    {!! Form::submitButton(__('Update')) !!}

    {!! Form::close() !!}

@stop
