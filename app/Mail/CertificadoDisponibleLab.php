<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CertificadoDisponibleLab extends Mailable
{
    use Queueable, SerializesModels;

    public $nombreLab;
    public $gestion;
    public $url;

    /**
     * @param  string  $nombreLab  Nombre del laboratorio destinatario.
     * @param  string  $gestion    Gestión de los certificados publicados.
     * @param  string  $url        Enlace a la pantalla "Mis certificados" del laboratorio.
     */
    public function __construct($nombreLab, $gestion, $url)
    {
        $this->nombreLab = $nombreLab;
        $this->gestion = $gestion;
        $this->url = $url;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Certificado disponible | PEEC - INLASA');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.certificado-disponible',
            with: [
                'nombreLab' => $this->nombreLab,
                'gestion'   => $this->gestion,
                'url'       => $this->url,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
