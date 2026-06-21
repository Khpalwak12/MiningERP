@extends('reports.pdf.layout')

@section('content')
<table>
    <thead>
        <tr>
            <th>{{ __('erp.fields.date') }}</th>
            <th>{{ __('erp.fields.customer') }}</th>
            <th>{{ __('erp.fields.quantity_ton') }}</th>
            <th>{{ __('erp.fields.price_per_ton') }}</th>
            <th>{{ __('erp.fields.total_amount') }}</th>
            <th>{{ __('erp.fields.status') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td>{{ \App\Support\JalaliDate::fromGregorian($row->shipment_date) }}</td>
            <td>{{ $row->customer?->name }}</td>
            <td>{{ $row->quantity_ton !== null ? number_format($row->quantity_ton, 3) : '—' }}</td>
            <td>{{ $row->price_per_ton !== null ? number_format($row->price_per_ton, 2) : '—' }}</td>
            <td>{{ $row->total_amount !== null ? number_format($row->total_amount, 2) : '—' }}</td>
            <td>{{ __('erp.shipments.statuses.'.$row->status) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
