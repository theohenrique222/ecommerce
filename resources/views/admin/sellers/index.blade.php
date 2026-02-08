@extends('adminlte::page')
@section('content_header')
<h1>Seller Index</h1>
<div class="text-center mb-4">
    <form action="{{ route('sellers.create') }}" method="get">
        <x-adminlte-button type="submit" label="Cadastrar Vendedor" theme="success" icon="fas fa-plus" />
    </form>
</div>
@php
    $heads = [
        'ID',
        'Nome',
        ['label' => 'Email', 'width' => 40],
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
    @foreach ($sellers as $seller)
        <tr>
            <td>{{ $seller->id }}</td>
            <td>{{ $seller->user->name }}</td>
            <td>{{ $seller->user->email }}</td>
            <td>
                <nobr>
                    <form action="{{ route('sellers.edit', $seller->id) }}" method="get" style="display:inline;">
                        {!! $btnEdit !!}
                    </form>
                    <form action="{{ route('sellers.destroy', $seller->id) }}" method="post" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        {!! $btnDelete !!}
                    </form>
                    <form action="{{ route('sellers.show', $seller->id) }}" method="get" style="display:inline;">
                        {!! $btnDetails !!}
                    </form>
                </nobr>
            </td>
        </tr>
    @endforeach
</x-adminlte-datatable>


@stop