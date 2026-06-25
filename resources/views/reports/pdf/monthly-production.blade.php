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
    </tbody>
</table>
@endsection
