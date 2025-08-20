<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class KorisnikAPIController extends Controller
{
    public function show()
    {
        $korisnik = Auth::user();

        if (!$korisnik) {
            return response()->json(['message' => 'Niste autorizovani.'], 401);
        }

        
        return response()->json($korisnik, 200);
    }

    public function update(Request $request)
    {
        $korisnik = Auth::user();

        if (!$korisnik) {
            return response()->json(['message' => 'Niste autorizovani.'], 401);
        }

        try {
            $validatedData = $request->validate([
                'adresa' => 'sometimes|string|max:255',
                'stanje_na_racunu' => 'sometimes|numeric|min:0|max:1000000',
                'broj_telefona' => 'sometimes|string|max:20',
            ]);

            $korisnik->update($validatedData);

            return response()->json([
                'message' => 'Podaci su uspesno azurirani.',
                'korisnik' => $korisnik
            ], 200);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Doslo je do greske.'], 500);
        }
    }
}
