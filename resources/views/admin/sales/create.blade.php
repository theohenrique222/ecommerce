@extends('adminlte::page')

@section('content_header')
    <h1 class="mb-3">Venda</h1>
@stop

@section('content')
<form action="{{ route('sales.store') }}" method="post">
    @csrf
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-select name="seller_id" label="Vendedor">
                        <option value="">Selecione um vendedor</option>
                        @foreach ($sellers as $seller)
                            <option value="{{ $seller->id }}"
                                {{ old('seller_id', auth()->user()->seller->id ?? null) == $seller->id ? 'selected' : '' }}>
                                {{ $seller->user->name }}
                            </option>
                        @endforeach
                    </x-adminlte-select>
                </div>

                <div class="col-md-6">
                    <x-adminlte-select name="client_id" label="Cliente">
                        <option value="">Selecione um cliente</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}"
                                {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </x-adminlte-select>
                </div>
            </div>

            {{-- Produto --}}
            <div class="row">
                <div class="col-md-6">
                    <x-adminlte-select name="product_id" label="Produto">
                        <option value="">Selecione um produto</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}"
                                data-price="{{ $product->price }}"
                                {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </x-adminlte-select>
                </div>

                <div class="col-md-3">
                    <x-adminlte-input
                        name="price"
                        label="Valor"
                        readonly
                        value="{{ old('price')}}"
                    />
                </div>

                <div class="col-md-3">
                    <x-adminlte-select name="quantity" label="Quantidade">
                        @for ($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}"
                                {{ old('quantity', 1) == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </x-adminlte-select>
                </div>
            </div>

            <div class="col-md-3">
                    <x-adminlte-input
                        name="total"
                        label="Total"
                        readonly
                        value="{{ old('total')}}"
                    />
                </div>

        </div>

        {{-- Footer --}}
        <div class="card-footer text-right">
            <x-adminlte-button
                type="submit"
                label="Pagamento"
                theme="success"
                icon="fas fa-credit-card"
            />
        </div>
    </div>
</form>
@stop

{{-- @section('js')
<script>
    document.querySelector('[name="product_id"]').addEventListener('change', function () {
        const price = this.options[this.selectedIndex].dataset.price;
        document.querySelector('[name="price"]').value = price ?? '';
    });

    document.querySelector('[name="total"]').addEventListener('change', function () {
        const price = document.querySelector('[name="price"]').value;
        const quantity = document.querySelector('[name="quantity"]').value;
        this.value = price * quantity;
    });
    
</script>
@stop --}}

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const productSelect  = document.querySelector('[name="product_id"]');
    const priceInput     = document.querySelector('[name="price"]');
    const quantitySelect = document.querySelector('[name="quantity"]');
    const totalInput     = document.querySelector('[name="total"]');

    function calculateTotal() {
        const price = Number(priceInput.value || 0);
        const quantity = Number(quantitySelect.value || 0);
        totalInput.value = price * quantity;
    }

    // Produto → define preço
    productSelect.addEventListener('change', function () {
        const price = this.options[this.selectedIndex]?.dataset.price;
        priceInput.value = price ?? '';
        calculateTotal();
    });

    // Quantidade → recalcula total
    quantitySelect.addEventListener('change', calculateTotal);

});
</script>
@stop
