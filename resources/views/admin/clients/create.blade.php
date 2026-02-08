@extends('adminlte::page')
@section('content_header')
<h1>Cadastrar Cliente</h1>

<form action="{{ route('clients.store') }}" method="post">
    @csrf
    <div class="row">

        <x-adminlte-input name="name" label="Nome" placeholder="Nome do cliente" fgroup-class="col-md-6"
            disable-feedback />
        <x-adminlte-input name="cpf" label="CPF" placeholder="CPF do cliente" fgroup-class="col-md-2"
            disable-feedback />

        <div class="col-12 mt-3">
            <x-adminlte-button type="submit" label="Salvar" theme="success" icon="fas fa-save" />
        </div>
    </div>
</form>

@stop