@extends('reports.pdf.layout')

@section('content')
<table>
    <thead>
        <tr>
            <th>{{ __('erp.fields.customer') }}</th>
            <th>{{ __('erp.reports.total_sales') }}</th>
            <th>{{ __('erp.reports.total_payments') }}</th>
            <th>{{ __('erp.reports.outstanding_balance') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td>{{ $row->name }}</td>
            <td class="numeric">{{ number_format($row->total_sales ?? 0, 2) }}</td>
            <td class="numeric">{{ number_format($row->total_payments ?? 0, 2) }}</td>
            <td class="numeric">{{ number_format($row->outstanding_balance ?? 0, 2) }}</td>
        </tr>
        @endforeach
        @if($rows->isNotEmpty())
        <tr>
            <th>{{ __('erp.reports.totals') }}</th>
            <th class="numeric">{{ number_format($summary['total_sales'], 2) }}</th>
            <th class="numeric">{{ number_format($summary['total_payments'], 2) }}</th>
            <th class="numeric">{{ number_format($summary['outstanding_balance'], 2) }}</th>
        </tr>
        @endif
    </tbody>
</table>
@endsection
