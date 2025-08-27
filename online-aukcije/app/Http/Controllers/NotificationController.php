<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Korisnik;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationController extends Controller
{
    public function unreadCount()
    {
        $korisnik = auth()->user();
        if (!$korisnik) {
            return response()->json(['count' => 0], 401);
        }

        $unreadCount = $korisnik->unreadNotifications()->count();

        return response()->json([
            'count' => $unreadCount
        ]);
    }

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
