<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VerificarCorreoLab extends Notification 
{
    //implements ShouldQueue
    use \Illuminate\Bus\Queueable;

    protected $usuario;
    protected $laboratorio;
    public function __construct($usuario, $laboratorio)
    {
        $this->usuario = $usuario;
        $this->laboratorio = $laboratorio;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
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
            ->view('emails.lab-verify', [
                'usuario' => $this->usuario,
                'laboratorio' => $this->laboratorio,
                'url_verificacion' => $url_verificacion
            ]);
    }
}
