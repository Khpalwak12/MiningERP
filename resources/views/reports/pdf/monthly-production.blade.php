@extends('reports.pdf.layout')

@section('content')
<table>
    <thead>
        <tr>
            <th>{{ __('erp.report_filters.month') }}</th>
            <th>{{ __('erp.report_filters.quantity') }}</th>
            <th>{{ __('erp.report_filters.total_tons') }}</th>
            <th>{{ __('erp.reports.total_sales') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td>{{ $row['month'] }}</td>
            <td class="numeric">{{ $row['shipment_count'] }}</td>
            <td class="numeric">{{ number_format($row['total_tons'], 3) }}</td>
            <td class="numeric">{{ number_format($row['total_sales'], 2) }}</td>
        </tr>
        @endforeach
        @if(count($rows) > 0)
        <tr>
            <th>{{ __('erp.reports.totals') }}</th>
            <th class="numeric">{{ $summary['shipment_count'] }}</th>
            <th class="numeric">{{ number_format($summary['total_tons'], 3) }}</th>
            <th class="numeric">{{ number_format($summary['total_sales'], 2) }}</th>
        </tr>
        @endif
    </tbody>
</table>
@endsection
