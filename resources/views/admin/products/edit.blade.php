@extends('adminlte::page')
@section('content_header')
<h1>Editar Produto</h1>

<form action="{{ route('products.update', $product->id) }}" method="post">
    @csrf
    @method('PUT')
    <div class="row">

        <x-adminlte-input name="name" label="Nome" placeholder="Nome do produto" fgroup-class="col-md-6"
            disable-feedback />
        <x-adminlte-input name="price" label="Valor" type="number" placeholder="0,00" fgroup-class="col-md-2"
            disable-feedback />

        <div class="col-12 mt-3">
            <x-adminlte-button type="submit" label="Salvar" theme="success" icon="fas fa-save" />
        </div>
    </div>
</form>

@stop