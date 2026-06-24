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
    </tbody>
</table>
@endsection
