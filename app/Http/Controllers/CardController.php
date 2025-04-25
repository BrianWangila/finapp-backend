<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;

class CardController extends Controller
{
    public function index() {
        return auth()->user()->cards;
    }

    public function store(Request $request) {
        $data = $request->validate([
            'number' => 'required|string',
            'balance' => 'required|numeric',
            'expiry' => ['required', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'],
            'cvv' => 'required|string',
        ]);

        return auth()->user()->cards()->create($data);
    }
}
