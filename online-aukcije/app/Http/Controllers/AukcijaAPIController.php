<?php

namespace App\Http\Controllers;

use App\Models\Aukcija;
use Illuminate\Http\Request;
use App\Http\Resources\AukcijaResource;
use Illuminate\Support\Facades\Validator;

class AukcijaAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return AukcijaResource::collection(Aukcija::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pocetnaCena' => 'required|numeric|min:10',
            'datumPocetka' => 'required|date_format:Y-m-d H:i:s|after_or_equal:now',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Greška pri validaciji.',
                'errors' => $validator->errors()
            ], 400); // Bad Request
        }

        $request->merge(['trenutnaCena' => $request->input('trenutnaCena', $request->pocetnaCena)]);
        $request->merge(['statusAukcije' => $request->input('statusAukcije', 'predstojeca')]);


        $aukcija = Aukcija::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Aukcija uspešno kreirana.',
            'data' => new AukcijaResource($aukcija)
        ], 201); // Created
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
            'pocetnaCena' => 'sometimes|required|numeric|min:10',
            'trenutnaCena' => 'sometimes|required|numeric|min:10|gte:pocetnaCena',
            'datumPocetka' => 'sometimes|required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Greška pri validaciji.',
                'errors' => $validator->errors()
            ], 400);
        }

        $aukcija->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Aukcija uspešno ažurirana.',
            'data' => new AukcijaResource($aukcija)
        ], 200); // OK
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

        if ($aukcije->isEmpty()) {
            return response()->json([
                'success' => true, 
                'message' => 'Nema aukcija sa proizvodima u kategoriji "' . $nazivKategorije . '".',
                'data' => []
            ], 200); // OK
        }

        return AukcijaResource::collection($aukcije);
    }
}
