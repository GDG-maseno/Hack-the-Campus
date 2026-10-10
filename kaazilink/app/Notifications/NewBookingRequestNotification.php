<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Data\ServiceDirectory;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewBookingRequestNotification extends Notification
{
    use Queueable;

    public Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $clientName = $this->booking->client ? $this->booking->client->name : 'A client';
        
        // Resolve service name
        $serviceName = $this->booking->service_slug;
        $service = collect(ServiceDirectory::getServices())->firstWhere('slug', $this->booking->service_slug);
        if ($service) {
            $serviceName = $service['name'];
        } else {
            // Check provider services custom name
            $ps = $this->booking->provider->providerServices->firstWhere('service_slug', $this->booking->service_slug);
            if ($ps && $ps->custom_name) {
                $serviceName = $ps->custom_name;
            } else {
                $serviceName = ucwords(str_replace('-', ' ', $this->booking->service_slug));
            }
        }

        $mail = (new MailMessage)
            ->subject("New Booking Request from {$clientName} — Kaazilink")
            ->greeting("Jambo {$notifiable->name},")
            ->line("You have received a new booking request on Kaazilink for **{$serviceName}** from **{$clientName}**.");

        if ($this->booking->scheduled_at) {
            $scheduledDate = $this->booking->scheduled_at instanceof \Carbon\CarbonInterface
                ? $this->booking->scheduled_at
                : \Carbon\Carbon::parse($this->booking->scheduled_at);
            $mail->line("**Requested Time:** " . $scheduledDate->format('M d, Y \a\t g:i A'));
        }

        return $mail
            ->action('View & Manage Booking', route('provider.dashboard'))
            ->line('Log in to your Provider Hub to accept, coordinate, or update this request.')
            ->salutation("Asante,\nThe Kaazilink Team");
    }
}
