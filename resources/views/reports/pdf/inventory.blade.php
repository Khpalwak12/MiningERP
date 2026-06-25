@extends('reports.pdf.layout')

@section('content')
<table>
    <thead>
        <tr>
            <th>{{ __('erp.fields.date') }}</th>
            <th>{{ __('erp.fields.name') }}</th>
            <th>{{ __('erp.fields.sku') }}</th>
            <th>{{ __('erp.fields.category') }}</th>
            <th>{{ __('erp.fields.movement_type') }}</th>
            <th>{{ __('erp.fields.quantity') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td>{{ \App\Support\JalaliDate::fromGregorian($row->movement_date) }}</td>
            <td>{{ $row->inventoryItem?->name }}</td>
            <td>{{ $row->inventoryItem?->sku }}</td>
            <td>{{ $row->inventoryItem?->category ?? '—' }}</td>
            <td>{{ __('erp.movement_types.'.$row->movement_type) }}</td>
            <td class="numeric">{{ number_format($row->quantity, 3) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
