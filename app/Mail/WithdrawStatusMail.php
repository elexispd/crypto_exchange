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

class WithdrawStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $transaction;
    public $status;

    public function __construct(User $user, Transaction $transaction, $status)
    {
        $this->user = $user;
        $this->transaction = $transaction;
        $this->status = $status;
    }

    public function envelope(): Envelope
    {
        $subject = $this->status === 'approved'
            ? 'Withdrawal Approved - Coafcare'
            : 'Withdrawal Rejected - Coafcare';

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $view = $this->status === 'approved'
            ? 'emails.withdraw.approved'
            : 'emails.withdraw.rejected';

        return new Content(
            view: $view,
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
