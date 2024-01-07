@php
  $config = [
      'appName' => config('app.name'),
      'locale' => $locale = app()->getLocale(),
      'locales' => config('app.locales'),
      'githubAuth' => config('services.github.client_id'),
      'notion' => [
          'worker' => config('services.notion.worker'),
      ],
      'links' => config('links'),
      'production' => App::isProduction(),
      'hCaptchaSiteKey' => config('services.h_captcha.site_key'),
      'google_analytics_code' => config('services.google_analytics_code'),
      'amplitude_code' => config('services.amplitude_code'),
      'sentry_dsn' => config('services.sentry_vue_dsn'),
      'crisp_website_id' => config('services.crisp_website_id'),
      'ai_features_enabled' => !is_null(config('services.openai.api_key')),
      's3_enabled' => config('filesystems.default') === 's3',
      'paid_plans_enabled' => !is_null(config('cashier.key'))
  ];
  $target_form = [
    'base_url' => '/target-force/forms'
  ]
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @include('targetforce::layouts.partials.favicons')

    <title>
        @hasSection('title')
            @yield('title') |
        @endif
        {{ config('app.name') }}
    </title>

    <link href="{{ asset('vendor/targetforce/css/fontawesome-all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/targetforce/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset(mix('app.css', 'vendor/targetforce')) }}" rel="stylesheet">

    @stack('css')
    {{-- Global configuration object --}}
    <script>
        window.config = @json($config);
        window.$crisp = [];
        window.targetform = @json($target_form);
    </script>
</head>
<body>

@yield('htmlBody')

<script src="{{ asset('vendor/targetforce/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('vendor/targetforce/js/popper.min.js') }}"></script>
<script src="{{ asset('vendor/targetforce/js/bootstrap.min.js') }}"></script>

<script>
    $('.sidebar-toggle').click(function (e) {
        e.preventDefault();
        toggleElements();
    });

    function toggleElements() {
        $('.sidebar').toggleClass('d-none');
    }
</script>

@stack('js')

</body>
</html>
