<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Notification;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first(); // Get the first user (or create one)

        if ($user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'transaction',
                'message' => 'You sent $50 to John Doe.',
                'is_read' => false,
            ]);

            Notification::create([
                'user_id' => $user->id,
                'type' => 'system',
                'message' => 'Your account has been updated.',
                'is_read' => true,
            ]);
        }
    }
}