<?php

/**
 * NewContactMessageMail
 *
 * Mailable notification sent to the portfolio administrator upon receipt
 * of a new public contact message or client project inquiry.
 */

declare(strict_types=1);

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewContactMessageMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly ContactMessage $contactMessage,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subjectPrefix = match ($this->contactMessage->type?->value) {
            'project_inquiry' => '[طلب مشروع جديد]',
            'job_offer' => '[عرض عمل جديد]',
            'consultation' => '[طلب استشارة تقنية]',
            default => '[رسالة تواصل جديدة]',
        };

        $subjectTitle = $this->contactMessage->subject
            ? "{$subjectPrefix} {$this->contactMessage->subject}"
            : "{$subjectPrefix} من {$this->contactMessage->name}";

        return new Envelope(
            subject: $subjectTitle,
            replyTo: [
                new Address($this->contactMessage->email, $this->contactMessage->name),
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $adminUrl = url('/ar/admin/message/'.$this->contactMessage->id);

        return new Content(
            view: 'emails.new_contact_message',
            with: [
                'contactMessage' => $this->contactMessage,
                'adminUrl' => $adminUrl,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
