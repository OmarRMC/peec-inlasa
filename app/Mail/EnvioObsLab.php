<?php

namespace App\Mail;

use App\Models\Configuracion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnvioObsLab extends Mailable
{
    use Queueable, SerializesModels;

    protected $usuario;
    protected $laboratorio;

    protected $observaciones;
    protected $gestion;
    protected $informacionHtml;

    /**
     * Create a new message instance.
     */
    public function __construct($usuario, $laboratorio, $observaciones, $gestion, ?string $informacionHtml = null)
    {
        $this->usuario = $usuario;
        $this->laboratorio = $laboratorio;
        $this->observaciones = $observaciones;
        $this->gestion = $gestion;
        // $informacionHtml solo se pasa en la vista previa (contenido aún no guardado)
        $this->informacionHtml = Configuracion::informacionEmail('observacion', $usuario, $laboratorio, $gestion, $informacionHtml);
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Su inscripcion tiene observaciones',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.obs_docs_lab',
            with: [
                'usuario' => $this->usuario,
                'laboratorio' => $this->laboratorio,
                'observaciones' => $this->observaciones,
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
