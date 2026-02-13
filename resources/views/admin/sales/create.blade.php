@extends('adminlte::page')

@section('content_header')
<h1 class="mb-3">Venda</h1>
@stop
@section('css')
<style>
    .product-row {
        cursor: pointer;
    }

    /* hover */
    .product-row:hover {
        background-color: #f1fdfa;
        /* leve, só pra feedback */
    }

    /* selecionado */
    .product-row.selected {
        background-color: #e6fffa !important;
        /* teal */
    }
</style>
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
                            <option value="{{ $seller->id }}" {{ old('seller_id', auth()->user()->seller->id ?? null) == $seller->id ? 'selected' : '' }}>
                                {{ $seller->user->name }}
                            </option>
                        @endforeach
                    </x-adminlte-select>
                </div>

                <div class="col-md-6">
                    <x-adminlte-select name="client_id" label="Cliente">
                        <option value="">Selecione um cliente</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </x-adminlte-select>
                </div>
            </div>
            <div class="text-center mb-3">
                <x-adminlte-modal id="modalCustom" title="Produtos" size="lg" theme="teal" icon="fas fa-bell" v-centered
                    static-backdrop scrollable>
                    <div style="height:800px;">
                        <h1>Lista de produtos</h1>
                        <div class="">
                            <div class="text-center">
                                <x-adminlte-input id="productSearch" name="iSearch" label="Buscar"
                                    placeholder="Buscar Produto" igroup-size="md">
                                    <x-slot name="appendSlot">
                                        <x-adminlte-button theme="outline-success" label="Go!" />
                                    </x-slot>
                                    <x-slot name="prependSlot">
                                        <div class="input-group-text text-success">
                                            <i class="fas fa-search"></i>
                                        </div>
                                    </x-slot>
                                </x-adminlte-input>
                            </div>
                            <div>
                                @php
                                    $heads = [
                                        'Produto',
                                        ['label' => 'Valor', 'width' => 40],
                                        ['label' => 'Estoque', 'no-export' => true, 'width' => 5],
                                    ];
                                @endphp
                                <x-adminlte-datatable id="table1" :heads="$heads">
                                    @foreach ($products as $product)
                                        <tr class="product-row" data-id="{{ $product->id }}"
                                            data-name="{{ $product->name }}" data-price="{{ $product->price }}">
                                            <td class="product-name">{{ $product->name }}</td>
                                            <td>R$ {{ number_format($product->price, 2, ',', '.') }}</td>
                                            <td>10</td>
                                        </tr>
                                    @endforeach
                                </x-adminlte-datatable>
                            </div>
                        </div>
                    </div>
                    <x-slot name="footerSlot">
                        <x-adminlte-button class="mr-auto" theme="success" label="Adicionar" />
                        <x-adminlte-button theme="danger" label="Fechar" data-dismiss="modal" />
                    </x-slot>
                </x-adminlte-modal>
                {{-- Example button to open modal --}}
                <x-adminlte-button label="Adicionar Produto" data-toggle="modal" data-target="#modalCustom"
                    class="bg-teal" />
            </div>
            @php
                $heads = [
                    'Produto',
                    'Valor Unitário',
                    'Quantidade',
                    'Subtotal'
                ];
            @endphp

            <x-adminlte-datatable id="saleTable" :heads="$heads">
                <tbody id="sale-items"></tbody>
            </x-adminlte-datatable>

            {{-- <div class="col-md-3">
                <x-adminlte-input name="total" label="Total" readonly value="{{ old('total')}}" />
            </div> --}}
            <div class="col-md-3">
                <p>Total: R$ <span id="totalDisplay"><strong>0,00</strong></span></p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="card-footer text-right">
            <x-adminlte-button type="submit" label="Pagamento" theme="success" icon="fas fa-credit-card" />
        </div>
    </div>
</form>
@stop

@section('js')
<script>

    document.addEventListener('DOMContentLoaded', function () {

        const saleItems = document.getElementById('sale-items');
        const totalInput = document.querySelector('[name="total"]');
        const modal = $('#modalCustom');

        function updateTotal() {
            let total = 0;
            const totalDisplay = document.getElementById('totalDisplay');
            document.querySelectorAll('.item-subtotal').forEach(el => {
                total += Number(el.innerText);
            });
            totalDisplay.innerHTML = 'R$ <strong>' + total.toFixed(2) + '</strong>';
            updateTotal();
        }

        document.querySelectorAll('.product-row').forEach(row => {
            row.addEventListener('click', function () {
                document.querySelectorAll('.product-row')
                    .forEach(r => r.classList.remove('selected'));

                this.classList.add('selected');

                const id = this.dataset.id;
                const name = this.dataset.name;
                const price = Number(this.dataset.price);

                if (document.getElementById('product-' + id)) {
                    modal.modal('hide');
                    return;
                }

                const tr = document.createElement('tr');
                tr.id = 'product-' + id;

                tr.innerHTML = `
                <td>
                    ${name}
                    <input type="hidden" name="products[${id}][id]" value="${id}">
                </td>
                <td>R$ ${price.toFixed(2)}</td>
                <td>
                    <select name="products[${id}][quantity]" class="form-control quantity">
                        ${[...Array(10)].map((_, i) =>
                    `<option value="${i + 1}">${i + 1}</option>`
                ).join('')}
                    </select>
                </td>
                <td class="item-subtotal">${price.toFixed(2)}</td>
            `;

                saleItems.appendChild(tr);
                updateTotal();

                modal.modal('hide');
            });
        });

        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('quantity')) {
                const tr = e.target.closest('tr');
                const price = Number(
                    tr.children[1].innerText.replace('R$', '').replace(',', '.')
                );
                const qty = Number(e.target.value);

                tr.querySelector('.item-subtotal').innerText =
                    (price * qty).toFixed(2);

                updateTotal();
            }
        });

        document.getElementById('productSearch').addEventListener('keyup', function () {
            const search = this.value.toLowerCase();
            document.querySelectorAll('.product-row').forEach(row => {
                row.style.display = row.dataset.name.toLowerCase().includes(search)
                    ? ''
                    : 'none';
            });
        });

        modal.on('shown.bs.modal', function () {
            document.querySelectorAll('.product-row')
                .forEach(r => r.classList.remove('selected'));

            document.getElementById('productSearch').focus();
        });

    });
</script>


@stop