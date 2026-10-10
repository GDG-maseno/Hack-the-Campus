<?php

namespace App\Notifications;

use App\Models\KycSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class KycRejectedNotification extends Notification
{
    use Queueable;

    public KycSubmission $submission;
    public string $rejectionReason;

    public function __construct(KycSubmission $submission, string $rejectionReason)
    {
        $this->submission = $submission;
        $this->rejectionReason = $rejectionReason;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Important Update: Your Kaazilink Verification Status')
            ->greeting("Habari {$notifiable->name},")
            ->line('Thank you for submitting your verification documents to Kaazilink.')
            ->line('Upon careful review by our compliance team, your verification documents could not be approved at this time for the following reason:')
            ->line("**Reason for rejection:**")
            ->line("\"{$this->rejectionReason}\"")
            ->line('Tafadhali do not worry. You can re-upload clear, legible copies of your National ID and KRA PIN certificates to complete your verification.')
            ->action('Update Verification Documents', route('provider.onboarding'))
            ->line('If you believe this decision was made in error, please reply directly to this email or reach out to our support team.')
            ->salutation("Asante,\nThe Kaazilink Team");
    }
}
