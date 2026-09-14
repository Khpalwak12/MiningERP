@extends('reports.pdf.layout')

@section('content')
<table>
    <thead>
        <tr>
            <th>{{ __('erp.fields.date') }}</th>
            <th>{{ __('erp.fields.amount') }}</th>
            <th>{{ __('erp.fields.receipt_number') }}</th>
            <th>{{ __('erp.fields.received_by') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td>{{ \App\Support\JalaliDate::fromGregorian($row->payment_date) }}</td>
            <td class="numeric">{{ number_format($row->amount, 2) }}</td>
            <td>{{ $row->receipt_number ?? '—' }}</td>
            <td>{{ $row->received_by }}</td>
        </tr>
        @endforeach
        @if($rows->isNotEmpty())
        <tr>
            <th>{{ __('erp.reports.totals') }}</th>
            <th class="numeric">{{ number_format($summary['amount'], 2) }}</th>
            <th>—</th>
            <th>—</th>
        </tr>
        @endif
    </tbody>
</table>
@endsection
