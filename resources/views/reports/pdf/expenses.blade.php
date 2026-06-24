@extends('reports.pdf.layout')

@section('content')
<table>
    <thead>
        <tr>
            <th>{{ __('erp.fields.date') }}</th>
            <th>{{ __('erp.fields.category') }}</th>
            <th>{{ __('erp.fields.subcategory') }}</th>
            <th>{{ __('erp.fields.bill_number') }}</th>
            <th>{{ __('erp.fields.amount') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td class="numeric">{{ \App\Support\JalaliDate::fromGregorian($row->expense_date) }}</td>
            <td>{{ $row->category?->localized_name }}</td>
            <td>{{ $row->subcategory ?? '—' }}</td>
            <td>{{ $row->bill_number ?? '—' }}</td>
            <td class="numeric">{{ number_format($row->amount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
