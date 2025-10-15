<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnvioCodigoLab extends Mailable
{
    use Queueable, SerializesModels;

    protected $usuario;
    protected $laboratorio;

    protected $loginUrl;
    /**
     * Create a new message instance.
     */
    public function __construct($usuario, $laboratorio)
    {
        $this->usuario = $usuario;
        $this->laboratorio = $laboratorio;
        $this->loginUrl = route('login');
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenido al sistema PEEC - INLASA',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.laboratorio.envio-codigo',
            with: [
                'usuario' => $this->usuario,
                'laboratorio' => $this->laboratorio,
                'loginUrl' => $this->loginUrl,
            ],
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
