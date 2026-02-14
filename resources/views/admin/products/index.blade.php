@extends('adminlte::page')
@section('content_header')
@section('plugins.Datatables', true)
<h1>Produtos</h1>

<div class="text-center mb-4">
    <form action="{{ route('products.create') }}" method="get">
        <x-adminlte-button type="submit" label="Cadastrar Produto" theme="success" icon="fas fa-plus" />
    </form>
</div>

@php
    $heads = [
        'ID',
        'Nome',
        'Valor',
        'Ação'
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

<x-adminlte-datatable class="text-center" id="table1" :heads="$heads">
    @foreach ($products as $product)
        <tr>
            <td>{{ $product->id }}</td>
            <td class="product-name">{{ $product->name }}</td>
            <td>R$ {{ number_format($product->price, 2, ',', '.') }}</td>
            <td>
                <nobr>
                    <form action="{{ route('products.edit', $product->id) }}" method="get" style="display:inline;">
                        {!! $btnEdit !!}
                    </form>
                    <form action="{{ route('products.destroy', $product->id) }}" method="post" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        {!! $btnDelete !!}
                    </form>
                    <form action="{{ route('products.show', $product->id) }}" method="get" style="display:inline;">
                        {!! $btnDetails !!}
                    </form>
                </nobr>
            </td>
        </tr>
    @endforeach
</x-adminlte-datatable>
@stop
