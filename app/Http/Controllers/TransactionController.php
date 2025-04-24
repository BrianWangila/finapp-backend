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
        try {
            $data = $request->validate([
                'type' => 'required|in:withdraw,send,receive',
                'from_account' => 'required|string',
                'name' => 'nullable|string',
                'to_account' => 'nullable|string',
                'amount' => 'required|numeric',
                'type_of_purchase' => 'nullable|string',
                'bank_name' => 'nullable|string',
                'negative' => 'nullable|boolean',
                'logo' => 'nullable|string',
                'date' => 'nullable|date',
            ]);
    
            return $data;
            // return auth()->user()->transactions()->create($data);

        } catch (\Throwable $th) {
            $response = [
                "status" => 500,
                "message" => "Something went wrong",
                "error" => $th->getMessage()
            ];

            return response()->json($response, 500);
        }
        
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