<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LaboratorioVerificacionDatos extends Mailable
{
    use Queueable, SerializesModels;

    public $laboratorio;
    public $pais;
    public $departamento;
    public $provincia;
    public $municipio;
    public $categoria;
    public $tipo;
    public $nivel;

    /**
     * Create a new message instance.
     */
    public function __construct($laboratorio, $pais, $departamento, $provincia, $municipio, $categoria, $tipo, $nivel)
    {
        $this->laboratorio = $laboratorio;
        $this->pais = $pais;
        $this->departamento = $departamento;
        $this->provincia = $provincia;
        $this->municipio = $municipio;
        $this->categoria = $categoria;
        $this->tipo = $tipo;
        $this->nivel = $nivel;
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Laboratorio Verificacion  de los Datos',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.laboratorio.verificacion',
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
