@extends('reports.pdf.layout')

@section('content')
<table>
    <thead>
        <tr>
            <th>{{ __('erp.fields.date') }}</th>
            <th>{{ __('erp.fields.truck_number') }}</th>
            <th>{{ __('erp.fields.quantity_ton') }}</th>
            <th>{{ __('erp.fields.rate_per_ton') }}</th>
            <th>{{ __('erp.contractor_royalty.total_royalty') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td>{{ \App\Support\JalaliDate::fromGregorian($row->production_date) }}</td>
            <td>{{ $row->truck_number ?? '—' }}</td>
            <td class="numeric">{{ number_format($row->quantity_ton, 3) }}</td>
            <td class="numeric">{{ number_format($row->rate_per_ton, 2) }}</td>
            <td class="numeric">{{ number_format($row->total_royalty, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
