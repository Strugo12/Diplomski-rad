<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Trips;
use App\Models\User;
use Illuminate\Http\Request;

class TripsController extends Controller
{
    public function index(){
        $trips=Trips::all();
        if($trips=="[]"){
            return response(['message' => 'No trips available']);
        }
        else if(auth()->user()->role=="guide"){
            foreach($trips as $trip){
                if($trip->guide==auth()->user()->id){
                    $guide[]=$trip;
                }
            }
            return response(['trips' => $guide]);
        }
        else{
            return response(['trips' => $trips]);
        }
    }

    public function addTrip(Request $request){

        if(auth()->user()->role=="guest"){
            return response(['message' =>'This action is not allowed to guests'], 403);
        }
        else if(auth()->user()->role=="guide"){
            return response(['message' => 'This action is not allowed to guides'], 403);
        }
        else if(auth()->user()->role=="leader"){
        $fields = $request->validate([
            'title' => 'required|unique:trips',
            'destination' => 'required',
            'duration' => 'required',
            'date'=> 'required|date',
            'guide'=> 'required',
            'image'=> 'required',
            'description'=> 'required|string',
            'price'=> 'required|integer',
            'seats'=> 'required|integer',
            'remark'=> 'required|string',
          ]);


            $flag=0;
            $users=User::all();
            foreach($users as $user){
                if($user->id==$fields['guide'] && $user->role=="guide"){
                    $flag=1;
                }
            }
            if($flag==0){
                return response([ 'message' => "Wrong guide"]);
            }
          $trip = Trips::create([
            'title' => $fields['title'],
            'destination' => $fields['destination'],
            'duration' => $fields['duration'],
            'date' => $fields['date'],
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

    public function destroy(Trips $trip){
        if($trip=='[]'){
            return response([ 'message' => "Trip does not exist"], 404);
        }
        $trip->delete();
        return response([ 'message' => "Trip $trip->title is deleted!"]);
     }

    public function detail(Trips $trip){
        if($trip=='[]'){
            return response([ 'message' => "Trip does not exist"]. 404);
        }
        if(auth()->user()->role=="guide"){
            $passengers=$trip->seats-$trip->freeseats;
            return response([ 'trip' => $trip, 'passengers'=> $passengers]);
        }
        else{
            return response([ 'trip' => $trip]);
        }
    }

    public function edit(Trips $trip, Request $request){
        if($trip=='[]'){
            return response([ 'message' => "Trip does not exist"]. 404);
        }
        if(auth()->user()->role!="leader"){
            return response([ 'message' => 'This can only be done by leaders'], 403);
        }
        $fields = $request->validate([
            'title' => 'required|unique:trips',
            'destination' => 'required',
            'duration' => 'required',
            'date'=> 'required|date',
            'guide'=> 'required',
            'image'=> 'required',
            'description'=> 'required|string',
            'price'=> 'required|integer',
            'seats'=> 'required|integer',
            'remark'=> 'required|string',
          ]);
          $passengers=$trip->seats-$trip->freeseats;
          if($passengers>$fields['seats']){
            return response([ 'message' => "$passengers is occupied and your max number of seats is $request->seats"]);
          }
          $flag=0;
            $users=User::all();
            foreach($users as $user){
                if($user->id==$fields['guide'] && $user->role=="guide"){
                    $flag=1;
                }
            }
            if($flag==0){
                return response([ 'message' => "Wrong guide"]);
            }
          $trip->title=$fields['title'];
          $trip->destination=$fields['destination'];
          $trip->duration=$fields['duration'];
          $trip->date=$fields['date'];
          $trip->guide=$fields['guide'];
          $trip->image=$fields['image'];
          $trip->description=$fields['description'];
          $trip->price=$fields['price'];
          $trip->seats=$fields['seats'];
          $trip->remark=$fields['remark'];
          $trip->save();
          return response([ 'message' => "Successfully changed $trip->title"]);
    }



}