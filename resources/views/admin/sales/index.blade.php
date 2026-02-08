@extends('adminlte::page')
@section('content_header')
<h1>Venda</h1>

<div class="text-center mb-4">
    <form action="{{ route('sales.create') }}" method="get">
        <x-adminlte-button type="submit" label="Nova Venda" theme="success" icon="fas fa-plus" />
    </form>
</div>

@stop