<?php

namespace App\Http\Controllers;

use App\Models\Aukcija;
use App\Models\Korisnik;
use App\Models\Proizvod;
use Illuminate\Http\Request;
use App\Http\Resources\AukcijaResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AukcijaAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Aukcija::with('proizvodi');

        if ($request->has('status_aukcije')) {
            $query->where('status_aukcije', $request->input('status_aukcije'));
        }

        if ($request->has('sort_by')) {
            switch ($request->input('sort_by')) {
                case 'cena_asc':
                    $query->orderBy('pocetna_cena', 'asc');
                    break;
                case 'cena_desc':
                    $query->orderBy('pocetna_cena', 'desc');
                    break;
            }
        }

        if ($request->has('kategorija')) {
            $nazivKategorije = $request->input('kategorija');
            $query->whereHas('proizvodi', function ($subQuery) use ($nazivKategorije) {
                $subQuery->where('kategorija', 'like', '%' . $nazivKategorije . '%');
            });
        }

        $aukcije = $query->paginate(12);

        return AukcijaResource::collection($aukcije);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'naziv' => 'required|string|max:255',
        'pocetna_cena' => 'required|numeric|min:100|max:500000',
        'maksimalna_cena' => 'nullable|numeric|max:1000000',
        'datum_pocetka' => 'required|date_format:Y-m-d H:i:s|after_or_equal:now',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Greška pri validaciji.',
            'errors' => $validator->errors()
        ], 400);
    }

    $validatedData = $validator->validated();

    $validatedData['korisnik_id'] = auth()->id();
    $validatedData['trenutna_cena'] = null;
    $validatedData['status_aukcije'] = 'predstojeca';
    $validatedData['vreme_isteka'] = Carbon::parse($validatedData['datum_pocetka'])->addSeconds(300); 

    $aukcija = Aukcija::create($validatedData);

    return response()->json([
        'success' => true,
        'message' => 'Aukcija uspešno kreirana.',
        'data' => new AukcijaResource($aukcija)
        ], 201);
        
        $validator = Validator::make($request->all(), [
            'naziv' => 'required|string|max:255',
            'pocetna_cena' => 'required|numeric|min:0',
            'maksimalna_cena' => 'nullable|numeric|min:0|gt:pocetna_cena',
            
            'datum_pocetka' => 'required|date_format:Y-m-d H:i:s|after_or_equal:now',

            'proizvodi' => 'required|array|min:1',
            'proizvodi.*.naziv' => 'required|string|max:255',
            'proizvodi.*.opis' => 'required|string',
            'proizvodi.*.kategorija' => 'required|string|max:255',
            'proizvodi.*.stanje' => 'required|string|in:novo,kao novo,korisceno,osteceno',
            'proizvodi.*.slika_url' => 'nullable|url|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Greska pri validaciji podataka za kreiranje aukcije.',
                'errors' => $validator->errors()
            ], 400);
        }

        $aukcija = Aukcija::create([
            'korisnik_id' => Auth::id(),
            'naziv' => $request->input('naziv'),
            'pocetna_cena' => $request->input('pocetna_cena'),
            'maksimalna_cena' => $request->input('maksimalna_cena'),
            'datum_pocetka' => $request->input('datum_pocetka'),
            'status_aukcije' => 'predstojeca',
            'trenutna_cena' => null,
            'vreme_isteka' => null,
        ]);

        foreach ($request->input('proizvodi') as $productData) {
            $aukcija->proizvodi()->create($productData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Aukcija uspesno kreirana.',
            'data' => new AukcijaResource($aukcija->load('proizvodi'))
        ], 201);
}

    /**
     * Display the specified resource.
     */
    public function show(Aukcija $aukcija)
    {
        return new AukcijaResource($aukcija);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Aukcija $aukcija)
    {
        $validator = Validator::make($request->all(), [
            'naziv' => 'string|max:255',
            'pocetna_cena' => 'numeric|min:100|max:500000',
            'maksimalna_cena' => 'nullable|numeric|max:1000000',
            'datum_pocetka' => 'date_format:Y-m-d H:i:s|after_or_equal:now',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Greska pri validaciji.',
                'errors' => $validator->errors()
            ], 400);
     }
    
        $validatedData = $validator->validated();

        if (isset($validatedData['pocetna_cena']) && $aukcija->status_aukcije != 'predstojeca') {
            return response()->json([
                'success' => false,
                'message' => 'Pocetna cena se može menjati samo pre pocetka aukcije.'
            ], 403);
        }
        if (isset($validatedData['datum_pocetka']) && $aukcija->status_aukcije != 'predstojeca') {
            return response()->json([
                'success' => false,
                'message' => 'Datum pocetka se može menjati samo pre pocetka aukcije.'
            ], 403);
        }
    
        $aukcija->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Aukcija uspešno ažurirana.',
            'data' => new AukcijaResource($aukcija)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aukcija $aukcija)
    {
        $aukcija->delete();

        return response()->json([
            'success' => true,
            'message' => 'Aukcija uspešno obrisana.'
        ], 204); // No Content
    }

    public function pretragaPoKategoriji(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategorija' => 'required|string|max:255', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Greška pri validaciji parametra za kategoriju.',
                'errors' => $validator->errors()
            ], 400); // Bad Request
        }

        $nazivKategorije = $request->input('kategorija');

        $aukcije = Aukcija::whereHas('proizvodi', function ($query) use ($nazivKategorije) {
            $query->where('kategorija', $nazivKategorije);
        })->get();

        $nazivKategorije = $request->input('kategorija');      

        if ($aukcije->isEmpty()) {
            return response()->json([
                'success' => true, 
                'message' => 'Nema aukcija sa proizvodima u kategoriji "' . $nazivKategorije . '".',
                'data' => []
            ], 200); // OK
        }

        return AukcijaResource::collection($aukcije);
    }

    public function korisnickeAukcije(Korisnik $korisnik)
    {
        if (Auth::id() !== $korisnik->id) {
            return response()->json([
                'success' => false,
                'message' => 'Nemate pravo pristupa ovim aukcijama.'
            ], 403);
        }

        $aukcije = Aukcija::where('korisnik_id', $korisnik->id)
                          ->with('proizvodi')
                          ->get();

        if ($aukcije->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Ovaj korisnik nema kreiranih aukcija.',
                'data' => []
            ], 200);
        }

        return AukcijaResource::collection($aukcije);
    }

}
