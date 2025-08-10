<?php

namespace App\Http\Controllers;

use App\Models\Ponuda;
use Illuminate\Http\Request;
use App\Models\Aukcija;
use App\Http\Resources\PonudaResource;
use App\Http\Resources\AukcijaResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PonudaAPIController extends Controller
{

    public function postaviPonudu(Request $request, Aukcija $aukcija)
    {
        if ($aukcija->status_aukcije !== 'aktivna' || Carbon::now()->gt($aukcija->vreme_isteka)) {
            return response()->json([
                'success' => false,
                'message' => 'Aukcija nije aktivna.'
            ], 403);
        }
    
        $minimalniIznos = ($aukcija->trenutna_cena ?? $aukcija->pocetna_cena) + 100;
    
        $rules = [
            'iznos' => 'required|numeric|gt:' . $minimalniIznos,
            'korisnik_id' => 'required|exists:korisnik,id'
        ];
    
        if ($aukcija->maksimalna_cena !== null) {
            $rules['iznos'] .= '|lte:' . $aukcija->maksimalna_cena;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Greška pri validaciji ponude.',
                'errors' => $validator->errors()
            ], 400); 
        }
    
        $validatedData = $validator->validated();

        try {
            DB::beginTransaction();

            $vremeProduzenjaSekunde = 10;
            $novoVremeIstekaNaPonudu = Carbon::now()->addSeconds($vremeProduzenjaSekunde);

            if ($novoVremeIstekaNaPonudu->gt($aukcija->vreme_isteka)) {
                $aukcija->vreme_isteka = $novoVremeIstekaNaPonudu;
            }

            $ponuda = Ponuda::create([
                'korisnik_id' => $validatedData['korisnik_id'],
                'iznos' => $validatedData['iznos'],
                'vreme_ponude' => now(),
                'aukcija_id' => $aukcija->id,
            ]);

            $aukcija->trenutna_cena = $validatedData['iznos'];
            $aukcija->save(); 

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Došlo je do greške prilikom postavljanja ponude.',
                'error' => $e->getMessage()
            ], 500); 
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
