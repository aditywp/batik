<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

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
        // Mengatur Laravel agar menggunakan styling Tailwind untuk pagination
        Paginator::useTailwind();

        // Mengubah format email verifikasi menjadi Bahasa Indonesia
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verifikasi Alamat Email Anda — Batik Ifawati')
                ->greeting('Halo, ' . $notifiable->name . '!')
                ->line('Terima kasih telah mendaftar di Batik Ifawati. Silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda.')
                ->action('Verifikasi Email Saya', $url)
                ->line('Jika Anda tidak merasa mendaftar di sistem kami, silakan abaikan email ini.')
                ->salutation('Salam hangat, Tim Batik Ifawati');
        });
    }
}