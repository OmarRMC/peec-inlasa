<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VerificarCorreoUser extends Notification implements ShouldQueue
{
    //implements ShouldQueue
    use \Illuminate\Bus\Queueable;

    protected $usuario;
    public function __construct($usuario)
    {
        $this->usuario = $usuario;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        URL::forceRootUrl(config('app.url'));
        $url_verificacion = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        return (new MailMessage)
            ->subject('Verifica tu correo electrónico')
            ->view('emails.user-verify', [
                'usuario' => $this->usuario,
                'url_verificacion' => $url_verificacion
            ]);
    }
}
