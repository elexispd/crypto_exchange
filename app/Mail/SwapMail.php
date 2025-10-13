<?php

namespace App\Mail;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SwapMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $transaction;

    public function __construct(User $user, Transaction $transaction)
    {
        $this->user = $user;
        $this->transaction = $transaction;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Swap Completed - ' . $this->transaction->from_currency . ' to ' . $this->transaction->to_currency . ' - Coafcare',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.swap.swap',
            with: [
                'user' => $this->user,
                'transaction' => $this->transaction,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
