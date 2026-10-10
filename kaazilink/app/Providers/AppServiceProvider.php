<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Verified;
use App\Notifications\WelcomeNotification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        if (\Illuminate\Support\Facades\DB::connection() instanceof \Illuminate\Database\SQLiteConnection) {
            $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
            $pdo->sqliteCreateFunction('acos', 'acos', 1);
            $pdo->sqliteCreateFunction('cos', 'cos', 1);
            $pdo->sqliteCreateFunction('sin', 'sin', 1);
            $pdo->sqliteCreateFunction('radians', 'deg2rad', 1);
        }

        // Customize Email Verification notification to Kaazilink Kenyan brand voice
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Karibu Kaazilink — Verify Your Account')
                ->greeting("Jambo {$notifiable->name},")
                ->line('Karibu Kaazilink! We are thrilled to welcome you to Kenya\'s trusted community marketplace for everyday services.')
                ->line('Tafadhali click the button below to verify your email address and activate your account.')
                ->action('Verify Email Address', $url)
                ->line('If you did not create an account on Kaazilink, please disregard this email.')
                ->salutation("Asante,\nThe Kaazilink Team");
        });

        // Send Welcome Notification after email is verified
        Event::listen(Verified::class, function (Verified $event) {
            $event->user->notify(new WelcomeNotification());
        });
    }
}
