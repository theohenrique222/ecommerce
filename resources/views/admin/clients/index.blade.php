@extends('adminlte::page')
@section('content_header')
<h1>Clientes</h1>

<div class="text-center mb-4">
    <form action="{{ route('clients.create') }}" method="get">
        <x-adminlte-button type="submit" label="Cadastrar Cliente" theme="success" icon="fas fa-plus" />
    </form>
</div>

@php
    $heads = [
        'ID',
        'Nome',
        ['label' => 'CPF', 'width' => 40],
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
    @foreach ($clients as $client)
        <tr>
            <td>{{ $client->id }}</td>
            <td>{{ $client->name }}</td>
            <td>{{ number_format($client->cpf, 3, '-', '.') }}</td>
            <td>
                <nobr>
                    <form action="{{ route('clients.edit', $client->id) }}" method="get" style="display:inline;">
                        {!! $btnEdit !!}
                    </form>
                    <form action="{{ route('clients.destroy', $client->id) }}" method="post" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        {!! $btnDelete !!}
                    </form>
                    <form action="{{ route('clients.show', $client->id) }}" method="get" style="display:inline;">
                        {!! $btnDetails !!}
                    </form>
                </nobr>
            </td>
        </tr>
    @endforeach
</x-adminlte-datatable>

@stop