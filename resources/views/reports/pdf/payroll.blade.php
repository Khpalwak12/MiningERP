@extends('reports.pdf.layout')

@section('content')
<h2>{{ __('erp.reports.employee_salary_summary') }}</h2>
<table>
    <thead>
        <tr>
            <th>{{ __('erp.fields.employee') }}</th>
            <th>{{ __('erp.fields.salary') }}</th>
            <th>{{ __('erp.fields.months_worked') }}</th>
            <th>{{ __('erp.fields.total_earned_salary') }}</th>
            <th>{{ __('erp.fields.total_paid_salary') }}</th>
            <th>{{ __('erp.fields.remaining_balance') }}</th>
            <th>{{ __('erp.fields.overpaid_amount') }}</th>
            <th>{{ __('erp.fields.status') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($summaries as $row)
        <tr>
            <td>{{ $row['name'] }}</td>
            <td class="numeric">{{ number_format($row['monthly_salary'], 2) }}</td>
            <td class="numeric">{{ $row['months_worked'] }}</td>
            <td class="numeric">{{ number_format($row['total_earned_salary'], 2) }}</td>
            <td class="numeric">{{ number_format($row['total_paid_salary'], 2) }}</td>
            <td class="numeric">{{ number_format($row['remaining_balance'], 2) }}</td>
            <td class="numeric">{{ number_format($row['overpaid_amount'], 2) }}</td>
            <td>{{ __('erp.payroll_statuses.'.$row['payroll_status']) }}</td>
        </tr>
        @endforeach
        @if(count($summaries) > 0)
        <tr>
            <th>{{ __('erp.reports.totals') }}</th>
            <th>—</th>
            <th>—</th>
            <th class="numeric">{{ number_format($summary['total_earned_salary'], 2) }}</th>
            <th class="numeric">{{ number_format($summary['total_paid_salary'], 2) }}</th>
            <th class="numeric">{{ number_format($summary['remaining_balance'], 2) }}</th>
            <th class="numeric">{{ number_format($summary['overpaid_amount'], 2) }}</th>
            <th>—</th>
        </tr>
        @endif
    </tbody>
</table>

<h2 style="margin-top: 24px;">{{ __('erp.reports.payment_transactions') }}</h2>
<table>
    <thead>
        <tr>
            <th>{{ __('erp.fields.date') }}</th>
            <th>{{ __('erp.fields.employee') }}</th>
            <th>{{ __('erp.fields.amount') }}</th>
            <th>{{ __('erp.fields.payment_type') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td class="numeric">{{ \App\Support\JalaliDate::fromGregorian($row->payment_date) }}</td>
            <td>{{ $row->employee?->name }}</td>
            <td class="numeric">{{ number_format($row->amount, 2) }}</td>
            <td>{{ $row->payment_type }}</td>
        </tr>
        @endforeach
        @if($rows->isNotEmpty())
        <tr>
            <th>{{ __('erp.reports.totals') }}</th>
            <th>—</th>
            <th class="numeric">{{ number_format($summary['payment_amount'], 2) }}</th>
            <th>—</th>
        </tr>
        @endif
    </tbody>
</table>
@endsection
