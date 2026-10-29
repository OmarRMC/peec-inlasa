<?php

namespace App\Mail;

use App\Models\Configuracion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AprobarInscripcion extends Mailable
{
    use Queueable, SerializesModels;

    protected $usuario;
    protected $laboratorio;
    protected $gestion;
    protected $informacionHtml;
    /**
     * Create a new message instance.
     */
    public function __construct($usuario, $laboratorio, $gestion, ?string $informacionHtml = null)
    {
        $this->usuario = $usuario;
        $this->laboratorio = $laboratorio;
        $this->gestion = $gestion;
        $this->informacionHtml = Configuracion::informacionEmail('aprobacion', $usuario, $laboratorio, $gestion, $informacionHtml);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Su inscripción fue Aprobada',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.aprobar_inscripcion',
            with: [
                'usuario' => $this->usuario,
                'laboratorio' => $this->laboratorio,
                'gestion' => $this->gestion,
                'informacionHtml' => $this->informacionHtml,
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
