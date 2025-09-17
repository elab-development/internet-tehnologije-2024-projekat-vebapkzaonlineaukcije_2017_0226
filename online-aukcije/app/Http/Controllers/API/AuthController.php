<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Korisnik;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Http\Resources\KorisnikResource;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ime' => 'required|string|max:255',
            'prezime' => 'required|string|max:255',
            'email' => 'required|string|max:255|email|unique:korisnik,email',
            'password' => 'required|string|min:8',
            'broj_telefona' => 'required|string|max:255',
            'adresa' => 'required|string|max:255',
            'stanje_na_racunu' => 'required|numeric|max:1000000|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Greška pri validaciji.',
                'errors' => $validator->errors()
            ], 400);
        }

        $korisnik = Korisnik::create([
            'ime' => $request->ime,
            'prezime' => $request->prezime,
            'email' => $request->email,
            'lozinka' => Hash::make($request->password),
            'uloga' => 'ulogovan korisnik',
            'broj_telefona' => $request->broj_telefona,
            'adresa' => $request->adresa,
            'stanje_na_racunu' => $request->stanje_na_racunu,
        ]);

        $token = $korisnik->createToken('auth_token')->plainTextToken;

        return response()
		->json(['success'=> true,
                'message'=> "Registracija je uspesna",
                'data' => new KorisnikResource($korisnik),
                'access_token' => $token,
                'token_type' => 'Bearer'], 201);
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Greška pri validaciji.',
                'errors' => $validator->errors()
            ], 400); 
        }

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password]))
        {
            return response()
                ->json([
                    'success' => false,
                    'message' => 'Pogrešni kredencijali. Proverite email i lozinku.'
                ], 401);
        }

        $korisnik = Korisnik::where('email', $request['email'])->firstOrFail();

        $token = $korisnik->createToken('auth_token')->plainTextToken;

        return response()
            ->json([
                'success' => true,
                'message' => 'Uspešno ste se ulogovali, ' . $korisnik->ime . '.',
                'data' => new KorisnikResource($korisnik),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 200);
    }

    public function logout(Request $request)
    {
        $korisnik = Auth::user();

        if (!$korisnik) {
            return response()->json([
                'success' => false,
                'message' => 'Niste prijavljeni.'
            ], 401);
        }

        $korisnik->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Uspešno ste se odjavili.'
        ], 200);
    }
}
