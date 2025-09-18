<?php

namespace App\Http\Controllers;

use App\Models\Aukcija;
use App\Models\Korisnik;
use App\Models\Proizvod;
use App\Models\Ponuda;
use Illuminate\Http\Request;
use App\Http\Resources\AukcijaResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Jobs\StartAuctionJob;
use App\Jobs\EndAuctionJob;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class AukcijaAPIController extends Controller
{

    use AuthorizesRequests, ValidatesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Aukcija::with('proizvodi','ponude');

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
            'datum_pocetka' => 'required|date_format:Y-m-d H:i:s|after_or_equal:now',
            'proizvodi' => 'required|array|min:1',
            'proizvodi.*.naziv' => 'required|string|max:255',
            'proizvodi.*.opis' => 'required|string',
            'proizvodi.*.kategorija' => 'required|string|max:255',
            'proizvodi.*.stanje' => 'required|string|in:novo,kao novo,korisceno,osteceno',
            'proizvodi.*.slika' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Greska pri validaciji podataka za kreiranje aukcije.',
                'errors' => $validator->errors()
            ], 400);
        }
        
        $validatedData = $validator->validated();

        $aukcija = Aukcija::create([
            'korisnik_id' => Auth::id(),
            'naziv' => $validatedData['naziv'],
            'pocetna_cena' => $validatedData['pocetna_cena'],
            'datum_pocetka' => $validatedData['datum_pocetka'],
            'status_aukcije' => 'predstojeca',
            'trenutna_cena' => null,
            'vreme_isteka' => Carbon::parse($validatedData['datum_pocetka'])->addSeconds(100), 
        ]);

        foreach ($request->input('proizvodi') as $index => $productData) {
            if ($request->hasFile("proizvodi.{$index}.slika")) {
                $productFile = $request->file("proizvodi.{$index}.slika");
                
                $path = $productFile->store('proizvodi', 'public');

                $productData['slika_url'] = Storage::url($path);
            } else {
                $productData['slika_url'] = null;
            }
            
            $aukcija->proizvodi()->create($productData);
        }

        $startDateTime = Carbon::parse($aukcija->datum_pocetka);
        $endDateTime = Carbon::parse($aukcija->vreme_isteka);

        StartAuctionJob::dispatch($aukcija)->delay($startDateTime);
        EndAuctionJob::dispatch($aukcija)->delay($endDateTime);

        return response()->json([
            'success' => true,
            'message' => 'Aukcija i proizvodi uspešno kreirani.',
            'data' => new AukcijaResource($aukcija->load('proizvodi')) 
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Aukcija $aukcija)
    {
        $aukcija->load('proizvodi', 'ponude');
        $aukcija->ponude = $aukcija->ponude->sortByDesc('iznos');

        return new AukcijaResource($aukcija);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Aukcija $aukcija)
    {
        $validator = Validator::make($request->all(), [
            'naziv' => 'sometimes|required|string|max:255',
            'pocetna_cena' => 'sometimes|required|numeric|min:100|max:500000',
            'proizvodi' => 'sometimes|required|array|min:1',
            'proizvodi.*.naziv' => 'required_with:proizvodi|string|max:255',
            'proizvodi.*.opis' => 'required_with:proizvodi|string',
            'proizvodi.*.kategorija' => 'required_with:proizvodi|string|max:255',
            'proizvodi.*.stanje' => 'required_with:proizvodi|string|in:novo,kao novo,korisceno,osteceno',
            'proizvodi.*.slika' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 
            'message' => 'Greška pri validaciji.', 'errors' => $validator->errors()], 400);
        }
        
        if ($aukcija->status_aukcije !== 'predstojeca') {
            return response()->json(['success' => false, 
            'message' => 'Aukcija se može menjati samo dok je u statusu "predstojeća".'], 403);
        }

        DB::beginTransaction();
        try {
            $updateData = $request->only(['naziv', 'pocetna_cena']);

            $aukcija->update($updateData);

            if ($request->has('proizvodi')) {
                $aukcija->proizvodi()->delete();
                foreach ($request->input('proizvodi') as $index => $productData) {
                    
                    if ($request->hasFile("proizvodi.{$index}.slika")) {
                        $productFile = $request->file("proizvodi.{$index}.slika");
                        $path = $productFile->store('proizvodi', 'public');
                        $productData['slika_url'] = Storage::url($path);
                    } else {
                        $productData['slika_url'] = $productData['slika_url'] ?? null;
                    }
                    $aukcija->proizvodi()->create($productData);
                }
            }
            
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false,
            'message' => 'Došlo je do greške na serveru prilikom izmene.', 'error' => $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Aukcija uspešno ažurirana.',
            'data' => new AukcijaResource($aukcija->load('proizvodi'))
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aukcija $aukcija)
    {
        $this->authorize('delete', $aukcija);

        $aukcija->delete();

        return response()->json([
            'success' => true,
            'message' => 'Aukcija uspešno obrisana.'
        ], 204); // No Content
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
