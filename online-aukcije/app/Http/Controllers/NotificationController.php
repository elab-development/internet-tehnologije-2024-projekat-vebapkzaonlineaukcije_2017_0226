<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Korisnik;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationController extends Controller
{
    public function index()
    {
        $korisnik = auth()->user();
        if (!$korisnik) {
            return new JsonResource([]);
        }

        $notifications = $korisnik->notifications;
        $korisnik->unreadNotifications->markAsRead();

        return new JsonResource($notifications);
    }
}
