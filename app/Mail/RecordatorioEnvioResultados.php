<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecordatorioEnvioResultados extends Mailable
{
    use Queueable, SerializesModels;

    public $nombreLab;
    public $ensayo;
    public $ciclo;
    public $fechaLimite;
    public $fase;

    /**
     * @param  string       $nombreLab    Nombre del laboratorio.
     * @param  string|null  $ensayo       Descripción del ensayo de aptitud.
     * @param  string|null  $ciclo        Nombre del ciclo.
     * @param  string|null  $fechaLimite  Fecha fin de envío de resultados (Y-m-d).
     * @param  string       $fase         'apertura' o 'final'.
     */
    public function __construct($nombreLab, $ensayo, $ciclo, $fechaLimite, $fase = 'apertura')
    {
        $this->nombreLab = $nombreLab;
        $this->ensayo = $ensayo;
        $this->ciclo = $ciclo;
        $this->fechaLimite = $fechaLimite;
        $this->fase = $fase;
    }

    public function envelope(): Envelope
    {
        $subject = $this->fase === 'final'
            ? 'Recordatorio final: envío de resultados PEEC-INLASA'
            : 'Envío de resultados habilitado | PEEC-INLASA';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.recordatorio-envio-resultados',
            with: [
                'nombreLab'   => $this->nombreLab,
                'ensayo'      => $this->ensayo,
                'ciclo'       => $this->ciclo,
                'fechaLimite' => $this->fechaLimite,
                'fase'        => $this->fase,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
