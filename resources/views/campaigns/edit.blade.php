@extends('targetforce::layouts.app')

@section('title', __('Edit Post'))

@section('heading')
    {{ __('Edit Post') }}
@stop

@section('content')

    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header">
                    {{ __('Edit Post') }}
                </div>
                <div class="card-body">
                    <form action="{{ route('targetforce.posts.update', $post->id) }}" method="POST" class="form-horizontal">
                        @csrf
                        @method('PUT')
                        @include('targetforce::posts.partials.form')
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop
