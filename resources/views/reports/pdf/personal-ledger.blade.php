@extends('reports.pdf.layout')

@section('content')
<table>
    <tbody>
        <tr><th>{{ __('erp.personal_accounts.total_credit_afn') }}</th><td class="numeric">{{ number_format($summary['total_credit_afn'] ?? 0, 2) }}</td></tr>
        <tr><th>{{ __('erp.personal_accounts.total_payment_afn') }}</th><td class="numeric">{{ number_format($summary['total_payment_afn'] ?? 0, 2) }}</td></tr>
        <tr><th>{{ __('erp.personal_accounts.balance_afn') }}</th><td class="numeric">{{ number_format($summary['outstanding_afn'] ?? 0, 2) }}</td></tr>
        <tr><th>{{ __('erp.personal_accounts.total_credit_usd') }}</th><td class="numeric">{{ number_format($summary['total_credit_usd'] ?? 0, 2) }}</td></tr>
        <tr><th>{{ __('erp.personal_accounts.total_payment_usd') }}</th><td class="numeric">{{ number_format($summary['total_payment_usd'] ?? 0, 2) }}</td></tr>
        <tr><th>{{ __('erp.personal_accounts.balance_usd') }}</th><td class="numeric">{{ number_format($summary['outstanding_usd'] ?? 0, 2) }}</td></tr>
    </tbody>
</table>

<h3 style="margin-top: 24px;">{{ __('erp.personal_accounts.contact_balances') }}</h3>
<table>
    <thead>
        <tr>
            <th>{{ __('erp.fields.name') }}</th>
            <th>{{ __('erp.fields.type') }}</th>
            <th>{{ __('erp.personal_accounts.balance_afn') }}</th>
            <th>{{ __('erp.personal_accounts.balance_usd') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($contact_balances as $row)
        <tr>
            <td>{{ $row['name'] }}</td>
            <td>{{ __('erp.personal_contact_types.'.$row['contact_type']) }}</td>
            <td class="numeric">{{ number_format($row['balance_afn'], 2) }}</td>
            <td class="numeric">{{ number_format($row['balance_usd'], 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<h3 style="margin-top: 24px;">{{ __('erp.personal_accounts.ledger') }}</h3>
<table>
    <thead>
        <tr>
            <th>{{ __('erp.fields.date') }}</th>
            <th>{{ __('erp.fields.name') }}</th>
            <th>{{ __('erp.fields.type') }}</th>
            <th>{{ __('erp.fields.currency') }}</th>
            <th>{{ __('erp.personal_transaction_types.credit') }}</th>
            <th>{{ __('erp.personal_transaction_types.payment') }}</th>
            <th>{{ __('erp.fields.description') }}</th>
            <th>{{ __('erp.personal_accounts.running_balance') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $row)
        <tr>
            <td>{{ $row['date_shamsi'] }}</td>
            <td>{{ $row['contact_name'] }}</td>
            <td>{{ __('erp.personal_transaction_types.'.$row['type']) }}</td>
            <td>{{ __('erp.currencies.'.$row['currency']) }}</td>
            <td class="numeric">{{ $row['credit_amount'] !== null ? number_format($row['credit_amount'], 2) : '—' }}</td>
            <td class="numeric">{{ $row['payment_amount'] !== null ? number_format($row['payment_amount'], 2) : '—' }}</td>
            <td>{{ $row['description'] ?? '—' }}</td>
            <td class="numeric">{{ number_format($row['running_balance'], 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
