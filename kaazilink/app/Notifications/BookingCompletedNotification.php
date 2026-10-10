<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Data\ServiceDirectory;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BookingCompletedNotification extends Notification
{
    use Queueable;

    public Booking $booking;
    public string $completedByRole;

    public function __construct(Booking $booking, string $completedByRole = 'provider')
    {
        $this->booking = $booking;
        $this->completedByRole = $completedByRole;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $service = collect(ServiceDirectory::getServices())->firstWhere('slug', $this->booking->service_slug);
        $serviceName = $service ? $service['name'] : ucwords(str_replace('-', ' ', $this->booking->service_slug));

        $clientName = $this->booking->client ? $this->booking->client->name : 'Client';
        $providerName = $this->booking->provider ? $this->booking->provider->name : 'Provider';

        if ($this->completedByRole === 'provider') {
            // Recipient is the client
            $mail = (new MailMessage)
                ->subject("Your Kaazilink Service with {$providerName} is Complete!")
                ->greeting("Habari {$notifiable->name},")
                ->line("**{$providerName}** has marked your booking for **{$serviceName}** as completed.")
                ->line('Asante kwa kutumia Kaazilink! Your feedback helps our community thrive and ensures dependable service for everyone.')
                ->action('Leave a Review & Rate Service', route('dashboard.bookings'))
                ->line('Please take a moment to rate your experience.')
                ->salutation("Asante sana,\nThe Kaazilink Team");

            return $mail;
        } else {
            // Recipient is the provider
            $mail = (new MailMessage)
                ->subject("Booking Marked Completed by {$clientName} — Kaazilink")
                ->greeting("Hongera {$notifiable->name}!")
                ->line("**{$clientName}** has confirmed and marked your **{$serviceName}** booking as completed.")
                ->line('Asante sana kwa kazi nzuri na huduma bora.');

            return $mail
                ->action('View in Provider Hub', route('provider.dashboard'))
                ->salutation("Kazi Iendelee,\nThe Kaazilink Team");
        }
    }
}
