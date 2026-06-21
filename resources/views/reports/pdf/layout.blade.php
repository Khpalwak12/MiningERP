<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('erp.reports.title') }} - {{ $reportType }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .meta { color: #666; margin-bottom: 12px; }
    </style>
</head>
<body>
    <h1>{{ __('erp.reports.title') }}: {{ str_replace('-', ' ', ucfirst($reportType)) }}</h1>
    <div class="meta">{{ __('erp.reports.generated_at') }}: {{ $generatedAt }}</div>
    @yield('content')
</body>
</html>
