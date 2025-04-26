<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Card;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        return auth()->user()->transactions()->latest()->get();
    }


    public function show(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return $transaction;
    }


    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $transaction->delete();
        return response()->json(['message' => 'Transaction deleted']);
    }


    public function send(Request $request)
    {
        try {
            $data = $request->validate([
                'card_id' => 'required|exists:cards,id',
                'to_account' => 'required|string',
                'amount' => 'required|numeric|min:0.01',
                'name' => 'nullable|string',
                'bank_name' => 'nullable|string',
            ]);

            $card = Card::where('id', $data['card_id'])->where('user_id', Auth::id())->firstOrFail();
            if ($card->balance < $data['amount']) {
                return response()->json(['error' => 'Insufficient balance'], 400);
            }

            // Deduct from card balance
            $card->balance -= $data['amount'];
            $card->save();


            // Create transaction
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'card_id' => $data['card_id'],
                'type' => 'send',
                'from_account' => $card->number,
                'to_account' => $data['to_account'],
                'amount' => $data['amount'],
                'name' => $data['name'] ?? 'Recipient',
                'bank_name' => $data['bank_name'] ?? null,
                'negative' => true,
                'logo' => 'https://i.pravatar.cc/40?img=1', // Placeholder logo
                'date' => now(),
                'status' => 'Completed',
            ]);

            return response()->json($transaction, 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong',
                'error' => $th->getMessage(),
            ], 500);
        }
    }


    public function withdrawToMpesa(Request $request)
    {
        try {
            $data = $request->validate([
                'card_id' => 'required|exists:cards,id',
                'phone_number' => 'required|string|regex:/^\+254\d{9}$/',
                'amount' => 'required|numeric|min:0.01',
            ]);

            $card = Card::where('id', $data['card_id'])->where('user_id', Auth::id())->firstOrFail();
            if ($card->balance < $data['amount']) {
                return response()->json(['error' => 'Insufficient balance'], 400);
            }

            // Deduct from card balance
            $card->balance -= $data['amount'];
            $card->save();

            // Simulate MPESA API call (dummy response)
            $mpesaResponse = [
                'transaction_id' => 'MPESA' . rand(100000, 999999),
                'status' => 'Completed',
            ];

            // Create transaction
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'card_id' => $data['card_id'],
                'type' => 'withdraw',
                'from_account' => $card->number,
                'to_account' => $data['phone_number'],
                'amount' => $data['amount'],
                'name' => 'MPESA Withdrawal',
                'negative' => true,
                'logo' => 'https://i.pravatar.cc/40?img=2', // Placeholder logo
                'date' => now(),
                'status' => $mpesaResponse['status'],
            ]);

            return response()->json($transaction, 201);


        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    // public function withdrawToBank(Request $request)
    // {
    //     try {
    //         $data = $request->validate([
    //             'card_id' => 'required|exists:cards,id',
    //             'bank_account' => 'required|string',
    //             'amount' => 'required|numeric|min:0.01',
    //             'bank_name' => 'required|string',
    //         ]);

    //         $card = Card::where('id', $data['card_id'])->where('user_id', Auth::id())->firstOrFail();
    //         if ($card->balance < $data['amount']) {
    //             return response()->json(['error' => 'Insufficient balance'], 400);
    //         }

    //         // Deduct from card balance
    //         $card->balance -= $data['amount'];
    //         $card->save();

    //         // Simulate bank API call (dummy response)
    //         $bankResponse = [
    //             'transaction_id' => 'BANK' . rand(100000, 999999),
    //             'status' => 'Completed',
    //         ];

    //         // Create transaction
    //         $transaction = Transaction::create([
    //             'user_id' => Auth::id(),
    //             'card_id' => $data['card_id'],
    //             'type' => 'withdraw',
    //             'from_account' => $card->number,
    //             'to_account' => $data['bank_account'],
    //             'amount' => $data['amount'],
    //             'name' => $data['bank_name'],
    //             'negative' => true,
    //             'logo' => 'https://i.pravatar.cc/40?img=4', // Placeholder logo
    //             'date' => now(),
    //             'status' => $bankResponse['status'],
    //         ]);

    //         return response()->json($transaction, 201);

    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'status' => 500,
    //             'message' => 'Something went wrong',
    //             'error' => $th->getMessage(),
    //         ], 500);
    //     }
    // }


    public function deposit(Request $request)
    {
        try {
            $data = $request->validate([
                'card_id' => 'required|exists:cards,id',
                'from_account' => 'required|string', // Could be MPESA phone number or another card number
                'amount' => 'required|numeric|min:0.01',
                'source_type' => 'required|in:mpesa,card', // Source of deposit
            ]);

            $card = Card::where('id', $data['card_id'])->where('user_id', Auth::id())->firstOrFail();

            // Add to card balance
            $card->balance += $data['amount'];
            $card->save();

            // Simulate source deduction if from another card (for now, just record the transaction)
            $name = $data['source_type'] === 'mpesa' ? 'MPESA Deposit' : 'Card Deposit';

            
            // Create transaction
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'card_id' => $data['card_id'],
                'type' => 'deposit',
                'from_account' => $data['from_account'],
                'to_account' => $card->number,
                'amount' => $data['amount'],
                'name' => $name,
                'negative' => false,
                'logo' => 'https://i.pravatar.cc/40?img=3', // Placeholder logo
                'date' => now(),
                'status' => 'Completed',
            ]);

            return response()->json($transaction, 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}