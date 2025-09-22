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
use App\Jobs\EndAuctionJob;
use Illuminate\Support\Facades\Log;

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
    
        $korisnik = auth()->user();

        if (!$korisnik) {
            Log::warning("PonudaAPIController: postaviPonudu - Korisnik nije prijavljen.");
            return response()->json([
                'success' => false,
                'message' => 'Morate biti prijavljeni da biste postavili ponudu.'
            ], 401);
        }

        $minimalniIznos = $aukcija->trenutna_cena ? $aukcija->trenutna_cena + 100 : $aukcija->pocetna_cena;
        
        $rules = [
            'iznos' => 'required|numeric|gte:' . $minimalniIznos . '|lte:' . $korisnik->stanje_na_racunu,
        ];

        $messages = [
            'iznos.gte' => 'Ponuda mora biti veća od trenutne cene.',
            'iznos.lte' => 'Nemate dovoljno sredstava na računu za ovu ponudu.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 400); 
        }
    
        $validatedData = $validator->validated();

        try {
            DB::beginTransaction();

            $svezaAukcija = Aukcija::lockForUpdate()->find($aukcija->id);

            if ($svezaAukcija->status_aukcije !== 'aktivna' || 
                Carbon::now()->gt($svezaAukcija->vreme_isteka)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Aukcija je istekla pre nego što je ponuda postavljena'
                ], 409);
            }

            $minimalniIznosSvezeAukcije = $svezaAukcija->trenutna_cena 
            ? $svezaAukcija->trenutna_cena + 100 : $svezaAukcija->pocetna_cena;
            if ($validatedData['iznos'] < $minimalniIznosSvezeAukcije) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Ponuda je niža od nove minimalne ponude (' . $minimalniIznosSvezeAukcije . ' RSD).'
                ], 409);
            }

            $sekundeDoIsteka = Carbon::now()->diffInSeconds($svezaAukcija->vreme_isteka, false);
            $granicaZaProduzenje = 30;
            $vremeProduzenja = 10;

            if ($sekundeDoIsteka > 0 && $sekundeDoIsteka <= $granicaZaProduzenje) {
                $svezaAukcija->vreme_isteka = $svezaAukcija->vreme_isteka->addSeconds($vremeProduzenja);
                EndAuctionJob::dispatch($svezaAukcija)->delay($svezaAukcija->vreme_isteka);
            }

            $ponuda = Ponuda::create([
                'korisnik_id' => $korisnik->id,
                'iznos' => $validatedData['iznos'],
                'vreme_ponude' => now(),
                'aukcija_id' => $svezaAukcija->id,
            ]);

            $svezaAukcija->trenutna_cena = $validatedData['iznos'];
            $svezaAukcija->save(); 

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("PonudaAPIController: postaviPonudu - Greška: " . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Došlo je do greške prilikom postavljanja ponude.',
                'error' => $e->getMessage()
            ], 500); 
        }

        return response()->json([
            'success' => true,
            'message' => 'Ponuda uspešno postavljena.',
            'data' => [
                'aukcija' => new AukcijaResource($svezaAukcija->load('proizvodi', 'ponude')) 
            ]
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
