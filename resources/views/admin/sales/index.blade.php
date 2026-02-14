@extends('adminlte::page')
@section('content_header')
<h1>Venda</h1>

<div class="text-center mb-4">
    <form action="{{ route('sales.create') }}" method="get">
        <x-adminlte-button type="submit" label="Nova Venda" theme="success" icon="fas fa-plus" />
    </form>
</div>

@php
    $statusMap = [
        'open' => 'Pendente',
        'paid' => 'Pago'
    ];
    $heads = [
        'Venda',
        'Vendedor',
        'Cliente',
        'Pagamento',
        'Ação',
    ];
    $btnDelete = '<button class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                                      <i class="fa fa-lg fa-fw fa-trash"></i>
                                  </button>';
    $btnDetails = '<button class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                                       <i class="fa fa-lg fa-fw fa-eye"></i>
                                   </button>';


@endphp

<x-adminlte-datatable class="text-center" id="table1" :heads="$heads">
    @foreach ($sales as $sale)
        <tr class="text-center">
            <td>{{ $sale->id }}</td>
            <td>{{ $sale->seller->user->name }}</td>
            <td>{{ $sale->client->name }}</td>
            <td>
                <span class="badge badge-{{ $sale->status == 'open' ? 'warning' : 'success' }}">
                    {{ $statusMap[$sale->status] ?? $sale->status }}
                </span>
            </td>
            <td>
                <nobr>
                    <form action="{{ route('sales.show', $sale->id) }}" method="get" style="display:inline;">
                        {!! $btnDetails !!}
                    </form>
                    <form action="{{ route('sales.destroy', $sale->id) }}" method="post" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        {!! $btnDelete !!}
                    </form>
                </nobr>
            </td>
        </tr>
    @endforeach
</x-adminlte-datatable>


@stop