<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Trips;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function post($tripId, Request $request){
        $trip=Trips::firstWhere('id',$tripId);
        if(!$trip){
            return response([ 'message' => "Trip does not exist"], 404);
        }

        if(auth()->user()->role=="guide" || auth()->user()->role=="leader"){
            return response([ 'message' => "This action is not allowed to you"], 403);
        }

        $fields = $request->validate([
            'seats' => 'required|max::8',
        ]);

        if($fields['seats']>8){
            return response([ 'message' => "You can reserve maximum 8 seats"], 400);
        }

        $reservations=Reservation::all();
        $flag=0;
        foreach($reservations as $reservation){
            if($reservation->user_id==auth()->user()->id && $trip->id==$reservation->trip_id){
                $flag=1;
                break;
            }
        }
        if($flag==1){
            return response([ 'message' => "You have already booked $reservation->occupied_seats seats"]);
        }
        else if($trip->free_seats==0){
            return response([ 'message' => "All seats reserved"]);
        }
        else if($trip->free_seats<$request->seats){
            return response([ 'message' => "Only $trip->free_seats seats is available"]);
        }
        else{
            Reservation::create([
                'trip_id' => $trip->id,
                'occupied_seats' => $fields['seats'],
                'user_id'=>auth()->user()->id,
            ]);
            $trip->free_seats=$trip->free_seats-$fields['seats'];
            $trip->save();
            return response([ 'message' => "You have successfully booked $request->seats seats"]);
        }
    }

    public function delete($tripId){

        $trip=Trips::firstWhere('id',$tripId);
        if(!$trip){
            return response([ 'message' => "Trip does not exist"], 404);
        }
        if(auth()->user()->role!="guest"){
            return response([ 'message' => 'This can only be done by guests'], 403);
        }
        $flag=0;
        $reservations=Reservation::all();
        foreach($reservations as $reservation){
            if($reservation->user_id==auth()->user()->id && $trip->id==$reservation->trip_id){
                $flag=1;
                break;
            }
        }
        if($flag==0){
            return response([ 'message' => "You have not booked your seats yet"]);
        }
        $trip->free_seats= $trip->free_seats+ $reservation->occupied_seats;
        $trip->save();
        $reservation->delete();
        return response([ 'message' => "You have successfully deleted your reservation"]);
    }

    public function getAllByTrip($tripId){

        $trip=Trips::firstWhere('id',$tripId);
        if(!$trip){
            return response([ 'message' => "Trip does not exist"], 404);
        }

        if(auth()->user()->role=="guide" || auth()->user()->role=="guest"){
            return response([ 'message' => "This action is not allowed to you"], 403);
        }

        $reservations=Reservation::all();


        if($reservations==null){
            return response([ 'message' => "There are no reservation available"], 200);
        }
        $result[]=[];
        $i=0;
        foreach($reservations as $reservation){
            if($trip->id==$reservation->trip_id){
                $result[$i]=['userId' => $reservation->user_id, 'reservedSeats' => $reservation->occupied_seats];
                $i++;
            }
        }
        if($result===[]){
            return response([ 'message' => "There are no reservation available"], 200);

        }
        return response($result);
    }

    public function deleteByLeader($tripId, Request $request){

        $trip=Trips::firstWhere('id',$tripId);
        if(!$trip){
            return response([ 'message' =>"Trip does not exist"], 404);
        }
        if(auth()->user()->role=="guide" || auth()->user()->role=="guest"){
            return response([ 'message' => "This action is not allowed to you"], 403);
        }


        $fields = $request->validate([
            'userId' => 'required',
        ]);
        $userId=$fields['userId'];

        $reservations=Reservation::all();
        if($reservations=='[]'){
            return response([ 'message' => "No reservations available"], 404);
        }
        foreach($reservations as $reservation){
            if($reservation->user_id==$userId && $reservation->trip_id==$trip->id ){
                break;
            }
        }
        if($reservation=='[]'){
            return response([ 'message' => "No reservation available"], 404);
        }
        $trip->free_seats= $trip->free_seats+ $reservation->occupied_seats;
        $trip->save();
        $reservation->delete();
        return response([ 'message' => "Successfully deleted reservation for $reservation->occupied_seats seats"]);
    }


    public function put($tripId, Request $request){
        $fields = $request->validate([
            'userId' => 'required',
            "seats" => 'required',
        ]);

        $trip=Trips::firstWhere('id',$tripId);
        if(!$trip){
            return response([ 'message' => "Trip does not exist"], 404);
        }
        if(auth()->user()->role=="guide" || auth()->user()->role=="guest"){
            return response([ 'message' => "This action is not allowed to you"], 403);
        }


        $userId=$fields['userId'];
        $seats=$fields['seats'];

        $reservations=Reservation::all();
        if($reservations=='[]'){
            return response([ 'message' => "No reservations available"], 404);
        }
        foreach($reservations as $reservation){
            if($reservation->user_id==$userId && $reservation->trip_id==$trip->id){
                break;
            }
        }
        if($reservation=='[]'){
            return response([ 'message' => "No reservation available"], 404);
        }
        if($seats>$reservation->occupied_seats){
            $newSeats=$seats-$reservation->occupied_seats;
            if($trip->free_seats<$newSeats){
                return response([ 'message' => "Only $trip->free_seats is available and the user has already reserved $reservation->occupied_seats seats "], 404);
            }
            $trip->free_seats= $trip->free_seats- $newSeats;
        }
        else if($seats<$reservation->occupied_seats){
            $newSeats=$reservation->occupied_seats-$seats;
            $trip->free_seats= $trip->free_seats+ $newSeats;
        }
        $trip->save();
        $reservation->occupied_seats=$seats;
        $reservation->save();

        return response([ 'message' => "Successfully edited reservation for $trip->title"]);
    }
}
