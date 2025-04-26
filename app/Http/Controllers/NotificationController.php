<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        return Notification::where('user_id', Auth::id())->latest()->get();
    }

    public function markAsRead(Request $request)
    {
        $request->validate(['id' => 'required|exists:notifications,id']);
        $notification = Notification::where('id', $request->id)->where('user_id', Auth::id())->firstOrFail();
        $notification->read = true;
        $notification->save();
        return response()->json(['message' => 'Notification marked as read']);
    }
}