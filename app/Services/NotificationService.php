<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\KycDocument;
use App\Models\Card;

class NotificationService
{
    // Notification types
    public const TYPE_SUCCESS = 'success';
    public const TYPE_INFO = 'info';
    public const TYPE_WARNING = 'warning';
    public const TYPE_ERROR = 'error';
    public const TYPE_SECURITY = 'security';

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
            'is_read' => false,
        ]);
    }

    /**
     * Send notification to multiple users
     */
    public static function sendToMany(array $users, string $title, string $message, string $type = 'info', array $data = null)
    {
        $notifications = [];
        $now = now();

        foreach ($users as $user) {
            $notifications[] = [
                'user_id' => $user->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'data' => $data,
                'is_read' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($notifications)) {
            Notification::insert($notifications);
        }
    }

    /**
     * Send notification to all users
     */
    public static function sendToAll(string $title, string $message, string $type = 'info', array $data = null)
    {
        $users = User::select('id')->get();
        self::sendToMany($users->toArray(), $title, $message, $type, $data);
    }

    // Wallet Notifications
    public static function walletCreated(Wallet $wallet): void
    {
        $user = $wallet->user;
        self::send(
            $user,
            'Wallet Created',
            'Your wallet has been successfully created and is ready to use.',
            self::TYPE_SUCCESS
        );
    }

    public static function walletBalanceUpdated(Wallet $wallet, string $currency, float $amount, string $type): void
    {
        $user = $wallet->user;
        $action = $type === 'credit' ? 'credited to' : 'debited from';
        
        self::send(
            $user,
            'Balance Updated',
            "Your {$currency} wallet has been {$action} with {$amount} {$currency}",
            $type === 'credit' ? self::TYPE_SUCCESS : self::TYPE_INFO,
            ['currency' => $currency, 'amount' => $amount, 'type' => $type]
        );
    }

    // Transaction Notifications
    public static function depositCreated(Transaction $transaction): void
    {
        $user = $transaction->user;
        $amount = $transaction->amount;
        $currency = strtoupper($transaction->currency);
        
        self::send(
            $user,
            'Deposit Initiated',
            "Your deposit of {$amount} {$currency} has been received and is being processed.",
            self::TYPE_INFO,
            ['transaction_id' => $transaction->id, 'type' => 'deposit']
        );
    }

    public static function depositCompleted(Transaction $transaction): void
    {
        $user = $transaction->user;
        $amount = $transaction->amount;
        $currency = strtoupper($transaction->currency);
        
        self::send(
            $user,
            'Deposit Completed',
            "Your deposit of {$amount} {$currency} has been successfully processed and is now available in your wallet.",
            self::TYPE_SUCCESS,
            ['transaction_id' => $transaction->id, 'type' => 'deposit']
        );
    }

    public static function withdrawalRequested(Transaction $withdrawal): void
    {
        $user = $withdrawal->user;
        $amount = $withdrawal->amount;
        $currency = strtoupper($withdrawal->currency);
        
        self::send(
            $user,
            'Withdrawal Requested',
            "Your withdrawal request for {$amount} {$currency} has been received and is being processed.",
            self::TYPE_INFO,
            ['transaction_id' => $withdrawal->id, 'type' => 'withdrawal']
        );
    }

    public static function withdrawalCompleted(Transaction $withdrawal): void
    {
        $user = $withdrawal->user;
        $amount = $withdrawal->amount;
        $currency = strtoupper($withdrawal->currency);
        
        self::send(
            $user,
            'Withdrawal Completed',
            "Your withdrawal of {$amount} {$currency} has been successfully processed and sent to your wallet.",
            self::TYPE_SUCCESS,
            ['transaction_id' => $withdrawal->id, 'type' => 'withdrawal']
        );
    }

    public static function swapCompleted(Transaction $swap): void
    {
        $user = $swap->user;
        $fromAmount = $swap->from_amount;
        $fromCurrency = strtoupper($swap->from_currency);
        $toAmount = $swap->to_amount;
        $toCurrency = strtoupper($swap->to_currency);
        
        self::send(
            $user,
            'Swap Completed',
            "You have successfully swapped {$fromAmount} {$fromCurrency} to {$toAmount} {$toCurrency}.",
            self::TYPE_SUCCESS,
            [
                'transaction_id' => $swap->id,
                'type' => 'swap',
                'from_amount' => $fromAmount,
                'from_currency' => $fromCurrency,
                'to_amount' => $toAmount,
                'to_currency' => $toCurrency
            ]
        );
    }

    // KYC Notifications
    public static function kycSubmitted(KycDocument $document): void
    {
        $user = $document->user;
        
        self::send(
            $user,
            'KYC Documents Submitted',
            'Your KYC documents have been received and are under review. We will notify you once the verification is complete.',
            self::TYPE_INFO,
            ['document_id' => $document->id, 'status' => 'submitted']
        );
    }

    public static function kycApproved(KycDocument $document): void
    {
        $user = $document->user;
        
        self::send(
            $user,
            'KYC Approved',
            'Congratulations! Your KYC verification has been approved. You now have full access to all platform features.',
            self::TYPE_SUCCESS,
            ['document_id' => $document->id, 'status' => 'approved']
        );
    }

    public static function kycRejected(KycDocument $document, string $reason): void
    {
        $user = $document->user;
        
        self::send(
            $user,
            'KYC Rejected',
            "Your KYC verification was not approved. Reason: {$reason}. Please update your documents and try again.",
            self::TYPE_ERROR,
            ['document_id' => $document->id, 'status' => 'rejected', 'reason' => $reason]
        );
    }

    // Card Notifications
    public static function cardLinked(Card $card): void
    {
        $user = $card->user;
        
        self::send(
            $user,
            'Card Linked',
            "Your card ending in " . substr($card->last_four, -4) . " has been successfully linked to your account.",
            self::TYPE_SUCCESS,
            ['card_id' => $card->id, 'last_four' => $card->last_four]
        );
    }

    // Security Notifications
    public static function loginDetected(User $user, array $ipInfo): void
    {
        $location = $ipInfo['city'] . ', ' . $ipInfo['region'] . ', ' . $ipInfo['country'];
        $device = $ipInfo['device'] ?? 'Unknown device';
        
        self::send(
            $user,
            'New Login Detected',
            "A new login was detected from {$location} using {$device}. If this wasn't you, please secure your account immediately.",
            self::TYPE_SECURITY,
            [
                'ip' => $ipInfo['ip'],
                'location' => $location,
                'device' => $device,
                'time' => now()->toDateTimeString()
            ]
        );
    }

    public static function passwordChanged(User $user): void
    {
        self::send(
            $user,
            'Password Changed',
            'Your password has been successfully changed. If you did not make this change, please contact support immediately.',
            self::TYPE_SECURITY,
            ['action' => 'password_change', 'time' => now()->toDateTimeString()]
        );
    }
}
