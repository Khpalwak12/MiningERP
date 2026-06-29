@extends('reports.pdf.layout')

@section('content')
<table>
    <thead>
        <tr>
            <th>{{ __('erp.fields.registration_date') }}</th>
            <th>{{ __('erp.fields.name') }}</th>
            <th>{{ __('erp.fields.related_to') }}</th>
            <th>{{ __('erp.fields.quantity') }}</th>
            <th>{{ __('erp.fields.unit') }}</th>
            <th>{{ __('erp.fields.status') }}</th>
            <th>{{ __('erp.fields.notes') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td class="numeric">{{ \App\Support\JalaliDate::fromGregorian($row->registration_date) }}</td>
            <td>{{ $row->name }}</td>
            <td>{{ $row->related_to ?? '—' }}</td>
            <td class="numeric">{{ number_format($row->quantity, 3) }}</td>
            <td>{{ __('erp.mine_asset_units.'.$row->unit) }}</td>
            <td>{{ __('erp.mine_asset_statuses.'.$row->status) }}</td>
            <td>{{ $row->remarks ?? '—' }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="3"><strong>{{ __('erp.fields.total_amount') }}</strong></td>
            <td class="numeric"><strong>{{ number_format($rows->sum('quantity'), 3) }}</strong></td>
            <td colspan="3"></td>
        </tr>
    </tbody>
</table>
@endsection
