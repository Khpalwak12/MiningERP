@extends('reports.pdf.layout')

@section('content')
<table>
    <thead>
        <tr>
            <th>{{ __('erp.fields.date') }}</th>
            <th>{{ __('erp.fields.customer') }}</th>
            <th>{{ __('erp.fields.mine_type') }}</th>
            <th>{{ __('erp.fields.stone_type') }}</th>
            <th>{{ __('erp.fields.quantity_ton') }}</th>
            <th>{{ __('erp.fields.price_per_ton') }}</th>
            <th>{{ __('erp.fields.total_amount') }}</th>
            <th>{{ __('erp.fields.status') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td class="numeric">{{ \App\Support\JalaliDate::fromGregorian($row->shipment_date) }}</td>
            <td>{{ $row->customer?->name }}</td>
            <td>{{ $row->mineType?->localized_name ?? '—' }}</td>
            <td>{{ $row->stoneType?->localized_name ?? '—' }}</td>
            <td class="numeric">{{ $row->quantity_ton !== null ? number_format($row->quantity_ton, 3) : '—' }}</td>
            <td class="numeric">{{ $row->price_per_ton !== null ? number_format($row->price_per_ton, 2) : '—' }}</td>
            <td class="numeric">{{ $row->total_amount !== null ? number_format($row->total_amount, 2) : '—' }}</td>
            <td>{{ __('erp.shipments.statuses.'.$row->status) }}</td>
        </tr>
        @endforeach
        @if($rows->isNotEmpty())
        <tr>
            <th>{{ __('erp.reports.totals') }}</th>
            <th>—</th>
            <th>—</th>
            <th>—</th>
            <th class="numeric">{{ number_format($summary['quantity_ton'], 3) }}</th>
            <th>—</th>
            <th class="numeric">{{ number_format($summary['total_amount'], 2) }}</th>
            <th>—</th>
        </tr>
        @endif
    </tbody>
</table>
@endsection
