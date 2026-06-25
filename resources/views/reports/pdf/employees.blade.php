@extends('reports.pdf.layout')

@section('content')
<table>
    <thead>
        <tr>
            <th>{{ __('erp.report_filters.employee_name') }}</th>
            <th>{{ __('erp.fields.father_name') }}</th>
            <th>{{ __('erp.fields.position') }}</th>
            <th>{{ __('erp.fields.phone') }}</th>
            <th>{{ __('erp.fields.status') }}</th>
            <th>{{ __('erp.fields.salary') }}</th>
            <th>{{ __('erp.fields.joining_date') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td>{{ $row->name }}</td>
            <td>{{ $row->father_name ?? '—' }}</td>
            <td>{{ $row->position ?? '—' }}</td>
            <td>{{ $row->phone ?? '—' }}</td>
            <td>{{ __('erp.status.'.$row->status) }}</td>
            <td class="numeric">{{ number_format($row->salary, 2) }}</td>
            <td>{{ \App\Support\JalaliDate::fromGregorian($row->joining_date) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
