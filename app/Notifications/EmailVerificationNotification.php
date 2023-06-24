<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        [$code, $url] = $this->createUrl($notifiable);
        if (!$code) {
            return (new MailMessage)
                ->greeting("Hola, {$notifiable->name}")
                ->line("Tu email ya ha sido verificado")
                ->line('¡Gracias por usar nuestra aplicacióm!');
        }

        return (new MailMessage)
            ->subject('Verificación de email')
            ->greeting("Hola, {$notifiable->name}")
            ->line("Por favor verifica to email haciendo click en el botón de abajo")
            ->action('Notification Action', $url)
            ->line('¡Gracias por usar nuestra aplicacióm!');
    }

    public function createUrl($notifiable)
    {
        $url_front = config('services.user_admin.front');
        $code = $notifiable->get_verification_email_code();
        if (!$code) {
            return [null, null];
        }

        $hashedCode = sha1($code->code);

        return [
            $code,
            create_url($url_front, "verification/email/{$code->id}/{$hashedCode}")
        ];
    }
}
