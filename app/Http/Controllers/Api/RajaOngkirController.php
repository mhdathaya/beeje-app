<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RajaOngkirController extends Controller
{   // Get cities based on province (for destination)
    public function searchDestination(Request $request)
    {
        $response = Http::withHeaders([
            'key' => config('rajaongkir.shipping_cost_key')
        ])->get('https://rajaongkir.komerce.id/api/v1/destination/domestic-destination', [
            'search'=> $request -> search,
            'limit'=> 100,
            'offset'=> 0
        ]);
        
    
        return response()->json($response[
            'data'
        ]);
    }
    
    public function CheckOngkir(Request $request)
    {
        $response = Http::withHeaders([
            'key' => config('rajaongkir.shipping_cost_key')
        ])->asForm()-> post ('https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
            'origin' => $request -> origin,
            'destination' => $request->destination,
            'weight' => $request->weight,
            'courier' => $request->courier,
        ]);
        
    
        return response()->json($response[
            'data'
        ]);
    }
   

}