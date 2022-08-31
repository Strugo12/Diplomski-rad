<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Trips;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TripsController extends Controller
{
    public function getAll(){
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

    public function post(Request $request){

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
            'durationDays' => 'required',
            'date'=> 'date',
            'guide'=> 'required',
            'imageUrl'=> 'required',
            'description'=> 'required|string',
            'price'=> 'required|integer',
            'seats'=> 'required|integer',
            'time'=>'required|date_format:H:i',
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
            'duration_days' => $fields['durationDays'],
            'date' => $fields['date'],
            'guide' => $fields['guide'],
            'image_url' => $fields['imageUrl'],
            'description' => $fields['description'],
            'price' => $fields['price'],
            'seats' => $fields['seats'],
            'free_seats' => $fields['seats'],
            'time'=>  $fields['time'],
            'remark' => $fields['remark'],
            ]);
        return response([ 'trip' => $trip]);
        }
    }

    public function delete($id){
        $trip=Trips::firstWhere('id',$id);
        if(!$trip){
            return response([ 'message' => "Trip does not exist"], 404);
        }
        if(auth()->user()->role=="guide"){
            return response(['message' => 'This action is not allowed to guides'], 403);
        }
        $reservations=Reservation::all();
        foreach($reservations as $reservation){
            if( $reservation->trip_id==$trip->id ){
               $reservation->delete();
            }
        }
        $trip->delete();
        return response([ 'message' => "Trip $trip->title is deleted!"]);
     }

    public function getById($id){
        $trip=Trips::firstWhere('id',$id);
        if(!$trip){
            return response([ 'message' => "Trip does not exist"], 404);
        }
        if(auth()->user()->role!="guest"){
            $reservations=Reservation::all();
            $passengers=$trip->seats-$trip->free_seats;
            if($reservations->count()>0){
                foreach($reservations as $reservation){
                    if($reservation->trip_id==$trip->id){
                        $list[]=$reservation;
                    }
                }
                return response([ 'trip' => $trip, 'passengers'=> $passengers, 'list'=>$list]);
            }

            return response([ 'trip' => $trip, 'passengers'=> $passengers]);
        }
        else{
            return response([ 'trip' => $trip]);
        }
    }

    public function put($id, Request $request){

        $trip=Trips::firstWhere('id',$id);
        if(!$trip){
            return response([ 'message' => "Trip does not exist"], 404);
        }

        if(auth()->user()->role!="leader"){
            return response([ 'message' => 'This can only be done by leaders'], 403);
        }
        $fields = $request->validate([
            'title' => 'required',
            'destination' => 'required',
            'durationDays' => 'required',
            'date'=> 'required|date',
            'guide'=> 'required',
            'imageUrl'=> 'required',
            'description'=> 'required|string',
            'price'=> 'required|integer',
            'seats'=> 'required|integer',
            'time'=>'required|date_format:H:i',
            'remark'=> 'required|string',
          ]);


          $tripTitle=Trips::firstWhere('title',$fields['title']);
          if($tripTitle && $tripTitle->id!==(int)$id){
              return response([ 'message' => "Trip title already exists"], 409);
          }


          $passengers=$trip->seats-$trip->free_seats;
          if($passengers>$fields['seats']){
            return response([ 'message' => "$passengers is occupied and your max number of seats is $request->seats"]);
          }
          $seatsDifference= $fields['seats']-$trip->seats;
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
          $trip->duration_days=$fields['durationDays'];
          $trip->date=$fields['date'];
          $trip->guide=$fields['guide'];
          $trip->image_url=$fields['imageUrl'];
          $trip->description=$fields['description'];
          $trip->price=$fields['price'];
          $trip->seats=$fields['seats'];
          $trip->remark=$fields['remark'];
          $trip->time=$fields['time'];
          $trip->free_seats=$trip->free_seats+ $seatsDifference;
          $trip->save();
          return response($trip);
    }



}
