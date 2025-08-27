<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Aukcija;

class AuctionEndedWithoutBids extends Notification
{
    use Queueable;

    protected $aukcija;

    /**
     * Create a new notification instance.
     */
    public function __construct(Aukcija $aukcija)
    {
        $this->aukcija=$aukcija;
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
        return [
            'aukcija_id' => $this->aukcija->id,
            'aukcija_naziv' => $this->aukcija->naziv,
            'poruka' => 'Vaša aukcija "' . $this->aukcija->naziv . '" je nazalost zavrsena bez ponuda.'
        ];
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
