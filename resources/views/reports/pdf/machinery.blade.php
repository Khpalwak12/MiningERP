@extends('reports.pdf.layout')

@section('content')
<table>
    <thead>
        <tr>
            <th>{{ __('erp.fields.date') }}</th>
            <th>{{ __('erp.fields.name') }}</th>
            <th>{{ __('erp.fields.bill_number') }}</th>
            <th>{{ __('erp.fields.currency') }}</th>
            <th>{{ __('erp.fields.amount') }}</th>
            <th>{{ __('erp.fields.description') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td class="numeric">{{ \App\Support\JalaliDate::fromGregorian($row->purchase_date) }}</td>
            <td>{{ $row->item_name }}</td>
            <td>{{ $row->bill_number ?? '—' }}</td>
            <td>{{ __('erp.currencies.'.$row->currency) }}</td>
            <td class="numeric">{{ number_format($row->amount, 2) }}</td>
            <td>{{ $row->description ?? '—' }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="4"><strong>{{ __('erp.machinery.total_afn') }}</strong></td>
            <td class="numeric"><strong>{{ number_format($rows->where('currency', 'AFN')->sum('amount'), 2) }}</strong></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="4"><strong>{{ __('erp.machinery.total_usd') }}</strong></td>
            <td class="numeric"><strong>{{ number_format($rows->where('currency', 'USD')->sum('amount'), 2) }}</strong></td>
            <td></td>
        </tr>
    </tbody>
</table>
@endsection
