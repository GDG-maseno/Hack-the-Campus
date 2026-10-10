<?php

namespace App\Notifications;

use App\Models\Review;
use App\Data\ServiceDirectory;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewReviewNotification extends Notification
{
    use Queueable;

    public Review $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $clientName = $this->review->client ? $this->review->client->name : 'A client';
        $stars = str_repeat('★', $this->review->rating) . str_repeat('☆', 5 - $this->review->rating);

        $mail = (new MailMessage)
            ->subject("New {$this->review->rating}-Star Review from {$clientName} on Kaazilink!")
            ->greeting("Habari {$notifiable->name}!")
            ->line("**{$clientName}** just posted a review for your service on Kaazilink:")
            ->line("**Rating:** {$stars} ({$this->review->rating} / 5)");

        if ($this->review->comment) {
            $mail->line("**Comment:** \"{$this->review->comment}\"");
        }

        return $mail
            ->action('View Your Public Profile', route('provider.profile', ['slug' => $notifiable->id]))
            ->line('Keep delivering quality and reliable work — glowing reviews build client confidence and earn you more Kaazi!')
            ->salutation("Hongera na Asante,\nThe Kaazilink Team");
    }
}
