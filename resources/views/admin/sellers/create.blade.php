@extends('adminlte::page')
@section('content_header')
<h1>Cadastrar Vendedor</h1>

<form action="{{ route('sellers.store') }}" method="post">
    @csrf
    <div class="row">
        <x-adminlte-input name="name" label="Nome" placeholder="Nome do Vendedor" fgroup-class="col-md-6"
            disable-feedback />
    </div>
    <div class="row">
        <x-adminlte-input name="email" label="Email" placeholder="Email do Vendedor" fgroup-class="col-md-6"
            disable-feedback />
    </div>
    <div class="row">
        <x-adminlte-input name="password" label="Senha" type="password" placeholder="Senha do Vendedor" fgroup-class="col-md-6"
            disable-feedback />
    </div>
    <div class="col-12 mt-3">
        <x-adminlte-button type="submit" label="Salvar" theme="success" icon="fas fa-save" />
    </div>
</form>

@stop