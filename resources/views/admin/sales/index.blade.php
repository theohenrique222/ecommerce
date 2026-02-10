@extends('adminlte::page')
@section('content_header')
<h1>Venda</h1>

<div class="text-center mb-4">
    <form action="{{ route('sales.create') }}" method="get">
        <x-adminlte-button type="submit" label="Nova Venda" theme="success" icon="fas fa-plus" />
    </form>
</div>

@php
    $heads = [
        'ID',
        'Vendedor',
        ['label' => 'Cliente', 'width' => 40],
        ['label' => 'Ação', 'no-export' => true, 'width' => 5],
    ];

    $btnEdit = '<button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                            <i class="fa fa-lg fa-fw fa-pen"></i>
                        </button>';
    $btnDelete = '<button class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                              <i class="fa fa-lg fa-fw fa-trash"></i>
                          </button>';
    $btnDetails = '<button class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                               <i class="fa fa-lg fa-fw fa-eye"></i>
                           </button>';


@endphp

<x-adminlte-datatable id="table1" :heads="$heads">
    @foreach ($sales as $sale)
        <tr>
            <td>{{ $sale->id }}</td>
            <td>{{ $sale->seller->user->name }}</td>
            <td>{{ $sale->client->name }}</td>
            <td>
                <nobr>
                    <form action="{{ route('sales.edit', $sale->id) }}" method="get" style="display:inline;">
                        {!! $btnEdit !!}
                    </form>
                    <form action="{{ route('sales.destroy', $sale->id) }}" method="post" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        {!! $btnDelete !!}
                    </form>
                    <form action="{{ route('sales.show', $sale->id) }}" method="get" style="display:inline;">
                        {!! $btnDetails !!}
                    </form>
                </nobr>
            </td>
        </tr>
    @endforeach
</x-adminlte-datatable>


@stop