<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificacionGeneralLab extends Mailable
{
    use Queueable, SerializesModels;

    public $nombreLab;
    public $titulo;
    public $cuerpoHtml;

    /**
     * @param  string  $nombreLab   Nombre del laboratorio destinatario.
     * @param  string  $titulo      Título de la notificación (asunto del correo).
     * @param  string  $cuerpoHtml  Cuerpo HTML de la notificación (tal como se escribió en el editor).
     *
     * Nota: la propiedad se llama $cuerpoHtml (no $html) porque Mailable ya usa
     * internamente una propiedad $html como cuerpo crudo y la colisión rompía la plantilla.
     */
    public function __construct($nombreLab, $titulo, $cuerpoHtml)
    {
        $this->nombreLab = $nombreLab;
        $this->titulo = $titulo;
        $this->cuerpoHtml = $cuerpoHtml;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->titulo);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notificacion-general',
            with: [
                'nombreLab' => $this->nombreLab,
                'titulo'    => $this->titulo,
                'html'      => $this->cuerpoHtml,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
