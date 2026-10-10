<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class WelcomeNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Karibu Kaazilink — Account Verified & Active! 🇰🇪')
            ->greeting("Jambo {$notifiable->name},")
            ->line('Karibu Kaazilink! Your email address has been successfully verified, and your account is ready to go.')
            ->line('Kaazilink connects Kenyan households and businesses with vetted local service professionals — from verified fundis, electricians, and plumbers to home care, technical repairs, and creative services.')
            ->line('**What would you like to do next?**')
            ->line('• **Looking for a service?** Browse our 51 categories and find verified professionals near you.')
            ->line('• **Offering a skill?** Complete your provider profile and get verified to start earning.')
            ->action('Browse Services Directory', route('services.browse'))
            ->line('Asante sana for joining our community of trust and dignified work.')
            ->salutation("Karibu Tena,\nThe Kaazilink Team");
    }
}
