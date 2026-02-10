<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Seller;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sales = Sale::with(['seller.user', 'product', 'client'])->get();
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
            'product_id' => 'required|exists:products,id',
            'client_id' => 'required|exists:clients,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);

        $total = $request->quantity * $request->price;

        Sale::create([
            'seller_id' => $request->seller_id,
            'product_id' => $request->product_id,
            'client_id' => $request->client_id,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'total' => $total,
        ]);



        return redirect()
            ->route('sales.index')
            ->with('success', 'Venda criada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        //
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
