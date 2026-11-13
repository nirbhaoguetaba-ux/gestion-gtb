<?php

namespace App\Mail;

use App\Models\Reparation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VehiculePretMail extends Mailable
{
    use Queueable, SerializesModels;

    public Reparation $reparation;

    /**
     * Create a new message instance.
     */
    public function __construct(Reparation $reparation)
    {
        $this->reparation = $reparation;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $vehicule = $this->reparation->vehicule;
        $immat = $vehicule ? $vehicule->registration_number : '';
        return new Envelope(
            subject: "Votre véhicule ({$immat}) est prêt pour restitution - Garage Mécano D-CLIC",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.vehicule_pret',
            with: [
                'reparation' => $this->reparation,
                'vehicule' => $this->reparation->vehicule,
                'client' => $this->reparation->vehicule?->client?->user,
                'technicien' => $this->reparation->mecanique?->user,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
