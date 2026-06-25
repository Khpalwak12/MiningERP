@extends('reports.pdf.layout')

@section('content')
<table>
    <thead>
        <tr>
            <th>{{ __('erp.fields.date') }}</th>
            <th>{{ __('erp.fields.quantity_ton') }}</th>
            <th>{{ __('erp.report_filters.created_by') }}</th>
            <th>{{ __('erp.fields.notes') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td>{{ \App\Support\JalaliDate::fromGregorian($row->shipment_date) }}</td>
            <td class="numeric">{{ $row->quantity_ton ?? '—' }}</td>
            <td>{{ $row->creator?->name ?? '—' }}</td>
            <td>{{ $row->notes ?? '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
