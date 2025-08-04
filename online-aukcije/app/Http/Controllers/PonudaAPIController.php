<?php

namespace App\Http\Controllers;

use App\Models\Ponuda;
use Illuminate\Http\Request;
use App\Models\Aukcija;
use App\Http\Resources\PonudaResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PonudaAPIController extends Controller
{

    public function postaviPonudu(Request $request, Aukcija $aukcija)
    {

        $validator = Validator::make($request->all(), [
        'iznos' => 'required|numeric|min:' . ($aukcija->trenutna_cena + 10),
        'korisnik_id' => 'required|exists:korisnik,id'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Greška pri validaciji ponude.',
            'errors' => $validator->errors()
        ], 400); 
    }

    if ($aukcija->status_aukcije !== 'aktivna') {
        return response()->json([
            'success' => false,
            'message' => 'Aukcija nije aktivna. Status: ' . $aukcija->status_aukcije
        ], 403);
    }
    
    try {
        DB::beginTransaction();

        $ponuda = Ponuda::create([
            'korisnik_id' => $request->korisnik_id,
            'iznos' => $request->iznos,
            'vreme_ponude' => now(),
            'aukcija_id' => $aukcija->id,
        ]);

        $aukcija->update(['trenutna_cena' => $request->iznos]);

        DB::commit();

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Došlo je do greške prilikom postavljanja ponude.',
            'error' => $e->getMessage()
        ], 500); // Internal Server Error
    }

    return response()->json([
        'success' => true,
        'message' => 'Ponuda uspešno postavljena.',
        'data' => new PonudaResource($ponuda)
    ], 201);
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
