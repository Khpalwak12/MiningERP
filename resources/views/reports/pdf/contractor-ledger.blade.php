@extends('reports.pdf.layout')

@section('content')
<table>
    <tbody>
        <tr><th>{{ __('erp.contractor_royalty.total_royalties') }}</th><td class="numeric">{{ number_format($summary['total_royalties'], 2) }}</td></tr>
        <tr><th>{{ __('erp.contractor_royalty.total_salary_charges') }}</th><td class="numeric">{{ number_format($summary['total_salary_charges'], 2) }}</td></tr>
        <tr><th>{{ __('erp.contractor_royalty.total_payments_received') }}</th><td class="numeric">{{ number_format($summary['total_payments'], 2) }}</td></tr>
        <tr><th>{{ __('erp.contractor_royalty.outstanding_balance') }}</th><td class="numeric">{{ number_format($summary['outstanding_balance'], 2) }}</td></tr>
    </tbody>
</table>

<h3 style="margin-top: 24px;">{{ __('erp.contractor_royalty.ledger') }}</h3>
<table>
    <thead>
        <tr>
            <th>{{ __('erp.fields.date') }}</th>
            <th>{{ __('erp.fields.type') }}</th>
            <th>{{ __('erp.fields.description') }}</th>
            <th>{{ __('erp.contractor_royalty.total_royalty') }}</th>
            <th>{{ __('erp.fields.amount') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $row)
        <tr>
            <td>{{ $row['date_shamsi'] }}</td>
            <td>
                @if($row['type'] === 'royalty')
                    {{ __('erp.contractor_royalty.royalty_entry') }}
                @elseif($row['type'] === 'salary_charge')
                    {{ __('erp.contractor_royalty.salary_charge_entry') }}
                @else
                    {{ __('erp.contractor_royalty.payment_entry') }}
                @endif
            </td>
            <td>{{ $row['description'] }}</td>
            <td class="numeric">{{ isset($row['royalty_amount']) ? number_format($row['royalty_amount'], 2) : '—' }}</td>
            <td class="numeric">{{ isset($row['payment_amount']) ? number_format($row['payment_amount'], 2) : '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
