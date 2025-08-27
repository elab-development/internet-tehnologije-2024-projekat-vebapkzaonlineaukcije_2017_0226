<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Aukcija;
use App\Models\Korisnik;

class AuctionEndedWithWinner extends Notification
{
    use Queueable;

    protected $aukcija;
    protected $pobednik;
    protected $kreator;

    /**
     * Create a new notification instance.
     */
    public function __construct(Aukcija $aukcija, Korisnik $pobednik, Korisnik $kreator)
    {
        $this->aukcija=$aukcija;
        $this->pobednik=$pobednik;
        $this->kreator=$kreator;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase(object $notifiable)
    {
        $data = [
            'aukcija_id' => $this->aukcija->id,
            'aukcija_naziv' => $this->aukcija->naziv,
            'pobednicka_cena' => $this->aukcija->trenutna_cena,
        ];
        
        if ($notifiable->id === $this->pobednik->id) {
            $data['tip'] = 'PobednikAukcije';
            $data['poruka'] = 'Cestitamo! Osvojili ste proizvode u aukciji "' . $this->aukcija->naziv . '". 
                Cena koju ste platili je "'. $this->aukcija->trenutna_cena .'" RSD.
                Ispod vam saljemo kontakt informacije kreatora aukcije kako biste mogli da se dogovorite
                za razmenu proizvoda aukcije';
            $data['kontakt_ime'] = $this->kreator->ime;
            $data['kontakt_prezime'] = $this->kreator->prezime;
            $data['kontakt_email'] = $this->kreator->email;
            $data['kontakt_telefon'] = $this->kreator->broj_telefona;
        } 
        else {
            $data['tip'] = 'KreatorAukcije';
            $data['poruka'] = 'Vaša aukcija "' . $this->aukcija->naziv . '" je uspešno završena. 
            Novcana vrednost koju ste dobili je "'. $this->aukcija->trenutna_cena .'" RSD.
            Ispod vam saljemo kontakt informacije pobednika aukcije kako biste mogli da se dogovorite
            za razmenu proizvoda aukcije';
            $data['kontakt_ime'] = $this->pobednik->ime;
            $data['kontakt_prezime'] = $this->pobednik->prezime;
            $data['kontakt_email'] = $this->pobednik->email;
            $data['kontakt_telefon'] = $this->pobednik->broj_telefona;
        }

        return $data;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
