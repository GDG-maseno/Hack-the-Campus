<?php

namespace App\Notifications;

use App\Models\KycSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class KycApprovedNotification extends Notification
{
    use Queueable;

    public KycSubmission $submission;

    public function __construct(KycSubmission $submission)
    {
        $this->submission = $submission;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Hongera! Your Kaazilink Provider Verification is Approved 🎉')
            ->greeting("Hongera {$notifiable->name}!")
            ->line('Your identity verification documents have been reviewed and successfully approved by the Kaazilink team.')
            ->line('Your profile is now officially marked with the **Verified Provider badge** and is visible to clients searching for trusted services across Kenya.')
            ->line('You are now ready to receive direct booking requests, display your services and pricing, and grow your livelihood with dignity and trust.')
            ->action('Open Provider Hub', route('provider.dashboard'))
            ->line('Pata Kaazi faster — keep your profile, service areas, and rates updated to win more clients.')
            ->salutation("Kazi Iendelee,\nThe Kaazilink Team");
    }
}
