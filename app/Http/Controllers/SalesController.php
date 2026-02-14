<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;
use App\Models\PaymentInstallment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sales = Sale::with(['seller.user', 'products', 'client'])->get();

        return view('admin.sales.index', compact('sales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sellers = Seller::all();
        $products = Product::all();
        $clients = Client::all();

        return view('admin.sales.create', compact('sellers', 'products', 'clients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|exists:sellers,id',
            'client_id' => 'required|exists:clients,id',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $sale = Sale::create([
            'seller_id' => $request->seller_id,
            'client_id' => $request->client_id,
            'total' => 0,
            'status' => 'open',
        ]);

        DB::transaction(function () use ($request, $sale) {

            $total = 0;

            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['id']);

                $subtotal = $product->price * $item['quantity'];

                $sale->products()->attach($product->id, [
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            $sale->update(['total' => $total]);
        });

        return redirect()
            ->route('sales.show', $sale)
            ->with('success', 'Venda criada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        $sale->load(['seller.user', 'products', 'client']);
        $payments = $sale->payments()->with('installments')->first();
        $methodLabel = [
            'credit_card' => 'Cartão de Crédito',
            'pix' => 'Pix',
            'cash' => 'Dinheiro',
            'custom' => 'Personalizado',
        ];

        $method = $payments?->method ?? null;

        $methodLabel = $methodLabel[$method] ?? 'Não definido';

        $installments = $sale->payments->first()->installments->first()->installment_number ?? 0;
        $amount = $sale->payments->first()->installments->first()->amount ?? 0;

        return view('admin.sales.show', compact('sale', 'payments', 'method', 'methodLabel', 'installments', 'amount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        return view('admin.sales.edit', compact('sale'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        //
    }

    public function storePayment(Request $request, Sale $sale)
    {
        $request->validate([
            'payment_method' => 'required',
            'installments' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $sale) {

            $payment = Payment::create([
                'sale_id' => $sale->id,
                'method' => $request->payment_method,
                'total' => $sale->total,
            ]);

            // pagamento parcelado (custom)
            if ($request->payment_method === 'custom') {

                $sum = collect($request->installments)->sum('amount');

                if ($sum != $sale->total) {
                    abort(400, 'Valor das parcelas não confere');
                }

                foreach ($request->installments as $i => $installment) {
                    PaymentInstallment::create([
                        'payment_id' => $payment->id,
                        'installment_number' => $i + 1,
                        'amount' => $installment['amount'],
                        'due_date' => $installment['due_date'] ?? null,
                    ]);
                }
            }

            if ($request->payment_method == 'credit_card') {
                PaymentInstallment::create([
                    'payment_id' => $payment->id,
                    'installment_number' => $request->credit_installments,
                    'amount' => $sale->total,
                    'due_date' => null,
                ]);
            }

            $sale->update(['status' => 'paid']);
        });

        return redirect()
            ->route('sales.index')
            ->with('success', 'Pagamento registrado com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        $sale->delete();

        return redirect()
            ->route('sales.index')
            ->with('success', 'Venda deletada com sucesso.');
    }
}
