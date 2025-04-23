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
            'to_account' => 'nullable|string',
            'amount' => 'required|numeric',
            'currency_from' => 'nullable|string',
            'currency_to' => 'nullable|string',
        ]);

        return auth()->user()->transactions()->create($data);
    }
}