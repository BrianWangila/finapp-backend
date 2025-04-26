<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use Illuminate\Support\Facades\Auth;

class CardController extends Controller
{
    public function index() {
        return auth()->user()->cards;
    }

    public function store(Request $request) {
        $data = $request->validate([
            'number' => 'required|string',
            'expiry' => ['required', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'],
            'cvv' => 'required|numeric|digits:3',
        ]);

        $card = Card::create([
            'user_id' => Auth::id(),
            'number' => $data['number'],
            'expiry' => $data['expiry'],
            'cvv' => $data['cvv'],
            'balance' => rand(10000, 100000) / 100,
        ]);

        return response()->json($card, 201);
        // return auth()->user()->cards()->create($data);
    }
}
