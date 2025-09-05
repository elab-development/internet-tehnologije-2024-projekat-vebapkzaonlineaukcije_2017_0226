<?php

namespace App\Http\Controllers;

use App\Models\Proizvod;
use Illuminate\Http\Request;
use App\Models\Aukcija;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ProizvodResource;
//PROVERI
class ProizvodAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Aukcija $aukcija)
    {
        return ProizvodResource::collection($aukcija->proizvodi);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Aukcija $aukcija)
    {
        $validator = Validator::make($request->all(), [
            'naziv' => 'required|string|max:255',
            'opis' => 'nullable|string',
            'kategorija' => 'required|string',
            'stanje' => 'required|string|in:novo,kao novo,korisceno,osteceno',
            'slika_url' => 'required|url'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Greška pri validaciji.',
                'errors' => $validator->errors()
            ], 400);
        }

        $proizvod = $aukcija->proizvodi()->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Proizvod uspešno dodat aukciji.',
            'data' => new ProizvodResource($proizvod)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Aukcija $aukcija, Proizvod $proizvod)
    {
        if ($proizvod->aukcija_id !== $aukcija->id) {
            return response()->json([
                'success' => false,
                'message' => 'Proizvod ne pripada datoj aukciji.'
            ], 404);
        }
        return new ProizvodResource($proizvod);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Aukcija $aukcija, Proizvod $proizvod)
    {
        if ($proizvod->aukcija_id !== $aukcija->id) {
            return response()->json([
                'success' => false,
                'message' => 'Proizvod ne pripada datoj aukciji, ne moze da se azurira.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'naziv' => 'sometimes|required|string|max:255',
            'opis' => 'nullable|string',
            'stanje' => 'sometimes|required|string|in:novo,kao novo,korisceno,osteceno',
            'slika_url' => 'sometimes|required|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Greška pri validaciji.',
                'errors' => $validator->errors()
            ], 400);
        }

        $proizvod->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Proizvod uspešno ažuriran.',
            'data' => new ProizvodResource($proizvod)
        ], 200);
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aukcija $aukcija, Proizvod $proizvod)
    {
        if ($proizvod->aukcija_id !== $aukcija->id) {
            return response()->json([
                'success' => false,
                'message' => 'Proizvod ne pripada datoj aukciji, ne može se obrisati u ovom kontekstu.'
            ], 404);
        }

        $proizvod->delete();

        return response()->json([
            'success' => true,
            'message' => 'Proizvod uspešno obrisan.'
        ], 204);
    
    }
}
