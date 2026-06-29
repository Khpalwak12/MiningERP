@extends('reports.pdf.layout')

@section('content')
<table>
    <thead>
        <tr>
            <th>{{ __('erp.fields.date') }}</th>
            <th>{{ __('erp.fields.name') }}</th>
            <th>{{ __('erp.fields.amount') }}</th>
            <th>{{ __('erp.fields.description') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td class="numeric">{{ \App\Support\JalaliDate::fromGregorian($row->expense_date) }}</td>
            <td>{{ $row->item_name }}</td>
            <td class="numeric">{{ number_format($row->amount, 2) }}</td>
            <td>{{ $row->description ?? '—' }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="2"><strong>{{ __('erp.fields.total_amount') }}</strong></td>
            <td class="numeric"><strong>{{ number_format($rows->sum('amount'), 2) }}</strong></td>
            <td></td>
        </tr>
    </tbody>
</table>
@endsection
