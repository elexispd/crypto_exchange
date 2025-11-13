<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Send notification to a user
     */
    public static function send(User $user, string $title, string $message, string $type = 'info', array $data = null)
    {
        return Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => $data,
        ]);
    }

    /**
     * Send notification to multiple users
     */
    public static function sendToMany(array $users, string $title, string $message, string $type = 'info', array $data = null)
    {
        $notifications = [];

        foreach ($users as $user) {
            $notifications[] = [
                'user_id' => $user->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'data' => $data,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Notification::insert($notifications);
    }

    /**
     * Send notification to all users
     */
    public static function sendToAll(string $title, string $message, string $type = 'info', array $data = null)
    {
        $users = User::all();
        self::sendToMany($users->toArray(), $title, $message, $type, $data);
    }
}
