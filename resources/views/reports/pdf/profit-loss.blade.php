@extends('reports.pdf.layout')

@section('content')
@if(!($matchesFilter ?? true))
<p>{{ __('erp.messages.no_records') }}</p>
@else
<table>
    <tbody>
        <tr><th>{{ __('erp.reports.marble_sales') }}</th><td class="numeric">{{ number_format($report['marble_sales'], 2) }}</td></tr>
        <tr><th>{{ __('erp.reports.sankari_sales') }}</th><td class="numeric">{{ number_format($report['sankari_sales'], 2) }}</td></tr>
        <tr><th>{{ __('erp.reports.total_income') }}</th><td class="numeric">{{ number_format($report['total_income'], 2) }}</td></tr>
        <tr><th>{{ __('erp.reports.operating_expenses') }}</th><td class="numeric">{{ number_format($report['operating_expenses'], 2) }}</td></tr>
        <tr><th>{{ __('erp.reports.payroll_expenses') }}</th><td class="numeric">{{ number_format($report['payroll_expenses'], 2) }}</td></tr>
        <tr><th>{{ __('erp.reports.total_expenses') }}</th><td class="numeric">{{ number_format($report['total_expenses'], 2) }}</td></tr>
        <tr><th>{{ __('erp.reports.net_profit') }}</th><td class="numeric">{{ number_format($report['net_profit'], 2) }}</td></tr>
    </tbody>
</table>
@endif
@endsection
