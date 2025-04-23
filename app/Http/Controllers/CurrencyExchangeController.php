<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CurrencyExchange;
use App\Models\User;



class CurrencyExchangeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return CurrencyExchange::where('user_id', $user->id)->latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_currency' => 'required|string|in:USD,CAD,EURO,AUD',
            'to_currency' => 'required|string|in:USD,CAD,EURO,AUD',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $exchangeRate = $this->getMockExchangeRate($request->from_currency, $request->to_currency);
        $converted = $request->amount * $exchangeRate;

        $exchange = CurrencyExchange::create([
            'user_id' => Auth::id(),
            'from_currency' => $request->from_currency,
            'to_currency' => $request->to_currency,
            'amount' => $request->amount,
            'converted_amount' => $converted
        ]);

        return response()->json($exchange, 201);
    }

    private function getMockExchangeRate($from, $to)
    {
        // Mock exchange rates
        $rates = [
            'USD' => ['CAD' => 1.3, 'EURO' => 0.9, 'AUD' => 1.5],
            'CAD' => ['USD' => 0.77, 'EURO' => 0.68, 'AUD' => 1.1],
            'EURO' => ['USD' => 1.1, 'CAD' => 1.47, 'AUD' => 1.6],
            'AUD' => ['USD' => 0.67, 'CAD' => 0.91, 'EURO' => 0.62]
        ];

        return $rates[$from][$to] ?? 1;
    }
}
