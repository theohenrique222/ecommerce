@extends('adminlte::page')

@section('title', 'Pagamento da Venda')

@section('content_header')
<h1>Finalizar Pagamento</h1>
@stop

@section('content')

@php
    $statusMap = [
        'open' => 'Pendente',
        'paid' => 'Pago'
    ]
@endphp

<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Informações da Venda</h3>
            </div>
            <div class="card-body">
                <p><strong>Vendedor:</strong> {{ $sale->seller->user->name }}</p>
                <p><strong>Cliente:</strong> {{ $sale->client->name }}</p>
                <p><strong>Pagamento:</strong>
                    <span class="badge badge-{{ $sale->status == 'open' ? 'warning' : 'success' }}">
                        {{-- {{ ucfirst($sale->status) }} --}}
                        {{ $statusMap[$sale->status] ?? $sale->status }}
                    </span>
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Resumo Financeiro</h3>
            </div>
            <div class="card-body text-center">
                <h2 class="text-success">
                    R$ {{ number_format($sale->total, 2, ',', '.') }}
                </h2>
            </div>
            @if ($sale->status != 'open')
            <div class="card-body text-center">
                <p class="text-info">
                    Forma de pagamento: 
                    <strong>
                        {{ $sale->payments->first()->method == 'custom' ? 'Parcelamento Personalizado' : $sale->payments->first()->method ?? 'Não definido' }}
                    </strong>
                </p>
                <p class="text-info">
                    Quantidade de parcelas: {{ $payments->first()->installments->count() ?? 0 }}
                </p>
            </div>
                
            @endif
        </div>
    </div>

</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Produtos da Venda</h3>
    </div>
    <div class="card-body">

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Valor Unitário</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->pivot->quantity }}</td>
                        <td>R$ {{ number_format($product->pivot->unit_price, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($product->pivot->subtotal, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>

@if($sale->status === 'open')

    <div class="card card-info">
        <div class="card-header">
            <h3 class="card-title">Forma de Pagamento</h3>
        </div>

        <div class="card-body">

            <form action="{{ route('sales.storePayment', $sale->id) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label><strong>Escolha a forma de pagamento</strong></label>

                    <div class="text-center mb-3">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCredit">
                            Cartão de Crédito
                        </button>

                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalCustom">
                            Parcelamento Personalizado
                        </button>

                        <button type="submit" name="payment_method" value="pix" class="btn btn-success">
                            PIX
                        </button>

                        <button type="submit" name="payment_method" value="cash" class="btn btn-secondary">
                            Dinheiro
                        </button>
                    </div>
                </div>

                {{-- CARTÃO --}}
                <div id="credit_installments_area" style="display:none;">
                    <h5>Parcelamento no Cartão</h5>

                    <div class="form-group">
                        <label>Quantidade de Parcelas</label>
                        <select name="credit_installments" id="credit_installments" class="form-control">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}">{{ $i }}x</option>
                            @endfor
                        </select>
                    </div>

                    <div class="alert alert-info mt-2" id="credit_installment_preview">
                        Valor da parcela: R$ 0,00
                    </div>
                </div>

                {{-- PERSONALIZADO --}}
                <div id="custom_installments_area" style="display:none;">
                    <h5>Parcelamento Personalizado</h5>

                    <div class="form-group">
                        <label>Quantidade de Parcelas</label>
                        <input type="number" id="installments_count" class="form-control" min="1" max="24">
                    </div>

                    <div id="installments_container"></div>
                </div>

                <div class="text-right mt-3">
                    <button type="submit" class="btn btn-success">
                        Confirmar Pagamento
                    </button>
                </div>

                <div class="modal fade" id="modalCredit">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-header bg-primary">
                                <h4 class="modal-title">Pagamento no Cartão</h4>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">

                                <div class="form-group">
                                    <label>Quantidade de Parcelas</label>
                                    <select name="credit_installments_modal" id="credit_installments_modal"
                                        class="form-control">
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}">{{ $i == 1 ? 'À vista' : $i . 'x' }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="alert alert-info text-center" id="credit_preview">
                                    Valor da parcela: R$ 0,00
                                </div>

                                <input type="hidden" name="payment_method" value="credit_card">


                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">
                                    Confirmar Pagamento
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Pagamento Personalizado dentro da modal --}}

                <div class="modal fade" id="modalCustom">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-header bg-info">
                                <h4 class="modal-title">Parcelamento Personalizado</h4>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">

                                <div class="form-group">
                                    <label>Quantidade de Parcelas</label>
                                    <input type="number" id="custom_count" class="form-control" min="1" max="24">
                                </div>

                                <div id="custom_container"></div>

                                <input type="hidden" name="payment_method" value="custom">

                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-info">
                                    Confirmar Pagamento
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endif



@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const total = parseFloat('{{ $sale->total }}') || 0;
        const creditSelect = document.getElementById('credit_installments_modal');
        const preview = document.getElementById('credit_preview');

        function updateCreditPreview() {
            const installments = parseFloat(creditSelect.value) || 1;
            const value = (total / installments).toFixed(2);
            preview.innerHTML = `${installments}x de R$ ${value.replace('.', ',')}`;
        }

        creditSelect.addEventListener('change', updateCreditPreview);

        updateCreditPreview();

        const countInput = document.getElementById('custom_count');
        const container = document.getElementById('custom_container');

        countInput.addEventListener('input', function () {

            container.innerHTML = '';
            const count = parseInt(this.value);

            if (!count) return;

            for (let i = 0; i < count; i++) {

                const div = document.createElement('div');
                div.classList.add('form-group');

                div.innerHTML = `
                <label>Parcela ${i + 1}</label>
                <input type="number" step="0.01"
                    name="installments[${i}][amount]"
                    class="form-control installment-input">
            `;

                container.appendChild(div);
            }

            const inputs = document.querySelectorAll('.installment-input');

            inputs.forEach((input) => {
                input.addEventListener('input', function () {

                    let sum = 0;

                    inputs.forEach((el, i) => {
                        if (i !== inputs.length - 1) {
                            sum += parseFloat(el.value) || 0;
                        }
                    });

                    const remaining = (total - sum).toFixed(2);
                    inputs[inputs.length - 1].value =
                        remaining > 0 ? remaining : 0;
                });
            });
        });

    });
</script>
@stop