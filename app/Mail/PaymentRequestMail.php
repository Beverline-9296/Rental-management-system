<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\ActivityLog;

class PaymentRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $landlord;
    public $tenant;
    public $assignment;
    public $amount;
    public $dueDate;
    public $customMessage;

    /**
     * Create a new message instance.
     */
    public function __construct($landlord, $tenant, $assignment, $amount, $dueDate, $customMessage = null)
    {
        $this->landlord = $landlord;
        $this->tenant = $tenant;
        $this->assignment = $assignment;
        $this->amount = $amount;
        $this->dueDate = $dueDate;
        $this->customMessage = $customMessage;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Request - ' . $this->assignment->property->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-request',
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
