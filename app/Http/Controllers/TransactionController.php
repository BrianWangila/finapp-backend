<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index() {
        return auth()->user()->transactions()->latest()->get();
    }

    public function store(Request $request) {
        $data = $request->validate([
            'type' => 'required|in:withdraw,send,exchange',
            'from_account' => 'required|string',
            'name' => 'string',
            'to_account' => 'nullable|string',
            'amount' => 'required|numeric',
            // 'currency_from' => 'nullable|string',
            // 'currency_to' => 'nullable|string',
            'type_of_purchase' => 'Appstore Purchase',
            'bank_name' => 'nullable|string',
            'negative' => 'nullable|boolean',
            'logo' => 'nullable|string',
            'date' => 'nullable|date',
        ]);

        return auth()->user()->transactions()->create($data);
    }


    public function show(Transaction $transaction){
        return $transaction;
    }


    public function update(Request $request, Transaction $transaction){
        $validated = $request->validate([
            'type' => 'required|string',
            'from_account' => 'required|string',
            'name' => 'required|string',
            'to_account' => 'required|string',
            'amount' => 'required|numeric',
            'type_of_purchase' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'negative' => 'required|boolean',
            'logo' => 'nullable|string',
            'date' => 'required|date'
        ]);

        $transaction->update($validated);
        return $transaction;
    }


    public function destroy(Transaction $transaction){
        $transaction->delete();
        return response()->json(['message' => 'Transaction deleted']);
    }

}