<?php

namespace App\Policies;

use App\Models\Aukcija;
use App\Models\Korisnik;
use Illuminate\Auth\Access\Response;

class AukcijaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Korisnik $korisnik): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Korisnik $korisnik, Aukcija $aukcija): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Korisnik $korisnik): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Korisnik $korisnik, Aukcija $aukcija): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Korisnik $korisnik, Aukcija $aukcija): bool
    {
        if ($korisnik->uloga === 'admin') {
            return true;
        }

        return $korisnik->id === $aukcija->korisnik_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Korisnik $korisnik, Aukcija $aukcija): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Korisnik $korisnik, Aukcija $aukcija): bool
    {
        return false;
    }
}
