<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Invest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvestmentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $investment;
    public $type; // 'initial', 'interest', 'completed'
    public $interestAmount;
    public $totalPayout;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Invest $investment, string $type = 'initial', float $interestAmount = null, float $totalPayout = null)
    {
        $this->user = $user;
        $this->investment = $investment;
        $this->type = $type;
        $this->interestAmount = $interestAmount;
        $this->totalPayout = $totalPayout;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->type) {
            'initial' => 'Investment Started - Coafcare',
            'interest' => 'Investment Interest Received - Coafcare',
            'completed' => 'Investment Completed - Coafcare',
            default => 'Investment Update - Coafcare'
        };

        return new Envelope(
            from: new Address('no_reply@coafcare.online', 'Coafcare'),
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.investment.investment',
            with: [
                'user' => $this->user,
                'investment' => $this->investment,
                'type' => $this->type,
                'interestAmount' => $this->interestAmount,
                'totalPayout' => $this->totalPayout,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
