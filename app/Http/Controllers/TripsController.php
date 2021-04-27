<?php

namespace App\Http\Controllers;
use App\Models\Trips;
use Illuminate\Http\Request;

class TripsController extends Controller
{
    public function index(){
        $trips=Trips::all();
        if($trips="[]"){
            return response(['message' => 'No trips available']);
        }
        return response(['trips' => $trips]);
    }

    public function addTrip(Request $request){
        if(auth()->user()->role="guest"){
            return response(['message' => 'Error']);
        }
        $fields = $request->validate([
            'title' => 'required|unique:trips',
            'destination' => 'required',
            'duration' => 'required',
            'date'=> 'required',
            'guide'=> 'required',
            'image'=> 'required',
            'description'=> 'required|string',
            'price'=> 'required|integer',
            'seats'=> 'required|integer',
            'remark'=> 'required|string',
          ]);

          $trip = Trips::create([
            'title' => $fields['title'],
            'destination' => $fields['destination'],
            'duration' => $fields['duration'],
            'guide' => $fields['guide'],
            'image' => $fields['image'],
            'description' => $fields['description'],
            'price' => $fields['price'],
            'seats' => $fields['seats'],
            'freeseats' => $fields['seats'],
            'remark' => $fields['remark'],
        ]);
        return response([ 'trip' => $trip]);
    }
}