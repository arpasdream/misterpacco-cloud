<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class SampleMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $content;

    /**
     * Create a new message instance.
     */
    public function __construct(array $content) {
        $this->content = $content;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->content['subject'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.sample',
        with: [
        'content' => $this->content
        ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if (!empty($this->content['attachment']))
        {
            $attachment = Attachment::fromPath($this->content['attachment']);

            return [
                $attachment
            ];
        }
        else
        {
            return [
            ];
        }

    }

}
