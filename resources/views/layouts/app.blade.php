@extends('targetforce::layouts.base')

@section('htmlBody')
    <div class="container-fluid">
        <div class="row">

            <div class="sidebar bg-purple-100 min-vh-100 d-none d-xl-block">

                <div class="mt-4">
                    <div class="logo text-center">
                        <a href="{{ route('targetforce.dashboard') }}">
                            <img src="{{ asset('/vendor/targetforce/img/logo-main.png') }}" alt="" width="175px">
                        </a>
                    </div>
                </div>

                <div class="mt-5">
                    @include('targetforce::layouts.partials.sidebar')
                </div>
            </div>

            @include('targetforce::layouts.main')
        </div>
    </div>
@endsection