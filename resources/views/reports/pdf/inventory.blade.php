@extends('reports.pdf.layout')

@section('content')
<table>
    <thead>
        <tr>
            <th>{{ __('erp.fields.name') }}</th>
            <th>{{ __('erp.fields.sku') }}</th>
            <th>{{ __('erp.fields.current_stock') }}</th>
            <th>{{ __('erp.fields.min_stock') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td>{{ $row->name }}</td>
            <td>{{ $row->sku }}</td>
            <td class="numeric">{{ number_format($row->current_stock, 3) }}</td>
            <td class="numeric">{{ number_format($row->min_stock, 3) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
