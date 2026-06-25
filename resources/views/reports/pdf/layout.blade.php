<!DOCTYPE html>
<html lang="{{ $locale ?? app()->getLocale() }}" dir="{{ ($isRtl ?? app()->getLocale() === 'ps') ? 'rtl' : 'ltr' }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ __('erp.reports.title') }} - {{ $reportType }}</title>
    <style>
        body {
            font-family: notosansarabic, sans-serif;
            font-size: 12px;
            direction: {{ ($isRtl ?? app()->getLocale() === 'ps') ? 'rtl' : 'ltr' }};
            text-align: {{ ($isRtl ?? app()->getLocale() === 'ps') ? 'right' : 'left' }};
        }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: {{ ($isRtl ?? app()->getLocale() === 'ps') ? 'right' : 'left' }};
        }
        th { background: #f3f4f6; font-weight: bold; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        h2 { font-size: 14px; margin: 16px 0 8px; }
        .meta { color: #666; margin-bottom: 12px; }
        .numeric { direction: ltr; unicode-bidi: embed; }
    </style>
</head>
<body>
    <h1>{{ __('erp.reports.title') }}: {{ $reportTitle ?? str_replace('-', ' ', ucfirst($reportType)) }}</h1>
    <div class="meta">{{ __('erp.reports.generated_at') }}: <span class="numeric">{{ $generatedAt }}</span></div>
    @if(!empty($filterSummary))
        <div class="meta">
            <strong>{{ __('erp.reports.applied_filters') }}:</strong>
            @foreach($filterSummary as $label => $value)
                <div>{{ $label }}: <span class="numeric">{{ $value }}</span></div>
            @endforeach
        </div>
    @endif
    @yield('content')
</body>
</html>
