<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificacionCorreoLaboratorio extends Mailable
{
    use Queueable, SerializesModels;
    public $usuario;
    public $laboratorio;
    public $url_verificacion;
    /**
     * Create a new message instance.
     */
    public function __construct($usuario, $laboratorio, $url_verificacion)
    {
        $this->usuario = $usuario;
        $this->laboratorio = $laboratorio;
        $this->url_verificacion = $url_verificacion;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verificacion Correo Laboratorio',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.laboratorio.verificacion',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
