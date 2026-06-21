@extends('reports.pdf.layout')

@section('content')
<table>
    <thead>
        <tr>
            <th>{{ __('erp.fields.date') }}</th>
            <th>{{ __('erp.fields.employee') }}</th>
            <th>{{ __('erp.fields.amount') }}</th>
            <th>{{ __('erp.fields.payment_type') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td>{{ \App\Support\JalaliDate::fromGregorian($row->payment_date) }}</td>
            <td>{{ $row->employee?->name }}</td>
            <td>{{ number_format($row->amount, 2) }}</td>
            <td>{{ $row->payment_type }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
