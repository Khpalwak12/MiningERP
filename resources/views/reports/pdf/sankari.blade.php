@extends('reports.pdf.layout')

@section('content')
<table>
    <thead>
        <tr>
            <th>{{ __('erp.fields.date') }}</th>
            <th>{{ __('erp.fields.truck_count') }}</th>
            <th>{{ __('erp.fields.price_per_truck') }}</th>
            <th>{{ __('erp.fields.subtotal') }}</th>
            <th>{{ __('erp.fields.discount') }}</th>
            <th>{{ __('erp.fields.total_amount') }}</th>
            <th>{{ __('erp.fields.notes') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td class="numeric">{{ \App\Support\JalaliDate::fromGregorian($row->sale_date) }}</td>
            <td class="numeric">{{ $row->truck_count }}</td>
            <td class="numeric">{{ number_format($row->price_per_truck, 2) }}</td>
            <td class="numeric">{{ number_format($row->subtotal(), 2) }}</td>
            <td class="numeric">{{ number_format($row->discount, 2) }}</td>
            <td class="numeric">{{ number_format($row->total_amount, 2) }}</td>
            <td>{{ $row->notes ?? '—' }}</td>
        </tr>
        @endforeach
        @if($rows->isNotEmpty())
        <tr>
            <th>{{ __('erp.reports.totals') }}</th>
            <th class="numeric">{{ $summary['truck_count'] }}</th>
            <th>—</th>
            <th class="numeric">{{ number_format($summary['subtotal'], 2) }}</th>
            <th class="numeric">{{ number_format($summary['discount'], 2) }}</th>
            <th class="numeric">{{ number_format($summary['total_amount'], 2) }}</th>
            <th>—</th>
        </tr>
        @endif
    </tbody>
</table>
@endsection
