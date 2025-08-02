<?php

namespace App\Http\Controllers;

use App\Models\Ponuda;
use Illuminate\Http\Request;
use App\Models\Aukcija;
use App\Http\Resources\PonudaResource;
use Illuminate\Support\Facades\Validator;

class PonudaAPIController extends Controller
{

    public function postaviPonudu(Request $request, Aukcija $aukcija)
    {
        
        $najvisaPonuda = $aukcija->trenutnaCena;

        $validator = Validator::make($request->all(), [
            'iznos' => 'required|numeric|min:' . ($najvisaPonuda + 10),
            'korisnikID' => 'required|exists:korisnik,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Greška pri validaciji ponude.',
                'errors' => $validator->errors()
            ], 400);  //Bad request
        }

        if ($aukcija->statusAukcije !== 'aktivna') {
             return response()->json([
                'success' => false,
                'message' => 'Aukcija nije aktivna. Status: ' . $aukcija->statusAukcije
            ], 403); // Forbidden
        }
        
        if ($request->iznos <= $najvisaPonuda) {
            return response()->json([
                'success' => false,
                'message' => 'Ponuda mora biti veća od trenutne najviše ponude.'
            ], 400); //Bad request
        }

        $ponuda = $aukcija->ponude()->create([
            'korisnikID' => $request->korisnikID,
            'iznos' => $request->iznos,
            'vremePonude' => now(),
        ]);

        $aukcija->update(['trenutnaCena' => $request->iznos]);

        return response()->json([
            'success' => true,
            'message' => 'Ponuda uspešno postavljena.',
            'data' => new PonudaResource($ponuda->load(['aukcija', 'korisnik']))
        ], 201); //Created
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Ponuda $ponuda)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ponuda $ponuda)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ponuda $ponuda)
    {
        //
    }
}
