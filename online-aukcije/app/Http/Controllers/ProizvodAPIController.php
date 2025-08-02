<?php

namespace App\Http\Controllers;

use App\Models\Proizvod;
use Illuminate\Http\Request;
use App\Models\Aukcija;
use Illuminate\Support\Facades\Validator;

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
            'slikaURL' => 'required|url'
            // ...ostala polja proizvoda
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Greška pri validaciji.',
                'errors' => $validator->errors()
            ], 400); // Bad request
        }

        $proizvod = $aukcija->proizvodi()->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Proizvod uspešno dodat aukciji.',
            'data' => new ProizvodResource($proizvod)
        ], 201); //Created
    }

    /**
     * Display the specified resource.
     */
    public function show(Proizvod $proizvod, Aukcija $aukcija)
    {
        if ($proizvod->aukcijaID !== $aukcija->id) {
            return response()->json([
                'success' => false,
                'message' => 'Proizvod ne pripada datoj aukciji.'
            ], 404); // Not Found
        }
        return new ProizvodResource($proizvod);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Aukcija $aukcija, Proizvod $proizvod)
    {
        if ($proizvod->aukcijaID !== $aukcija->id) {
            return response()->json([
                'success' => false,
                'message' => 'Proizvod ne pripada datoj aukciji, ne moze da se azurira.'
            ], 404); // Not Found
        }

        $validator = Validator::make($request->all(), [
            'naziv' => 'sometimes|required|string|max:255',
            'opis' => 'nullable|string', // 'nullable' jer možda želite dozvoliti da se opis obriše ili postavi na null
            'stanje' => 'sometimes|required|string|in:novo,kao novo,korisceno,osteceno',
            'slikaURL' => 'sometimes|required|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Greška pri validaciji.',
                'errors' => $validator->errors()
            ], 400); // Bad Request
        }

        $proizvod->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Proizvod uspešno ažuriran.',
            'data' => new ProizvodResource($proizvod)
        ], 200); // OK
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aukcija $aukcija, Proizvod $proizvod)
    {
        if ($proizvod->aukcijaID !== $aukcija->id) {
            return response()->json([
                'success' => false,
                'message' => 'Proizvod ne pripada datoj aukciji, ne može se obrisati u ovom kontekstu.'
            ], 404); // Not Found
        }

        $proizvod->delete();

        return response()->json([
            'success' => true,
            'message' => 'Proizvod uspešno obrisan.'
        ], 204); // uspesno brisanje
    
    }
}
