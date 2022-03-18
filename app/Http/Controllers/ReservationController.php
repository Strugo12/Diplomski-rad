<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Trips;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function reserve(Request $request, Trips $trip){
        if($trip=='[]'){
            return response([ 'message' => "Trip does not exist"]. 404);
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
            return response([ 'message' => "you have already booked $reservation->occupiedSeats seats"]);
        }
        else if($trip->freeseats==0){
            return response([ 'message' => "All seats reserved"]);
        }
        else if($trip->freeseats<$request->seats){
            return response([ 'message' => "Only $trip->freeseats seats is available"]);
        }
        else{
            Reservation::create([
                'trip_id' => $trip->id,
                'occupiedSeats' => $fields['seats'],
                'user_id'=>auth()->user()->id,
            ]);
            $trip->freeseats=$trip->freeseats-$fields['seats'];
            $trip->save();
            return response([ 'message' => "You have successfully booked $request->seats seats"]);
        }
    }

    public function destroy(Trips $trip){
        if($trip=='[]'){
            return response([ 'message' => "Trip does not exist"]. 404);
        }
        if(auth()->user()->role!="leader"){
            return response([ 'message' => 'This can only be done by leaders'], 403);
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
            return response([ 'message' => "you have not booked your seats yet"]);
        }
        $trip->freeseats= $trip->freeseats+ $reservation->occupiedSeats;
        $trip->save();
        $reservation->delete();
        return response([ 'message' => "You have successfully deleted your reservation"]);
    }

    public function reservations(Trips $trip){
        if($trip=='[]'){
            return response([ 'message' => "Trip does not exist"], 404);
        }
        if(auth()->user()->role=="guide" || auth()->user()->role=="guest"){
            return response([ 'message' => "This action is not allowed to you"], 403);
        }
        $reservations=Reservation::all();
       

        if($reservations==null){
            return response([ 'message' => "There are no reservation available"], 200);
        }
        foreach($reservations as $reservation){
            if($trip->id==$reservation->trip_id){
                $reservationUser[]=$reservation->user_id;
                $reservationSeats[]=$reservation->occupiedSeats;
            }
        }
        return response([ 'ReservationUser' =>  $reservationUser,'ReservationSeats' =>  $reservationSeats ]);
    }

    public function deleteReservation(Trips $trip, Request $request){
        if($trip=='[]'){
            return response([ 'message' => "Trip does not exist"], 404);
        }
        if(auth()->user()->role=="guide" || auth()->user()->role=="guest"){
            return response([ 'message' => "This action is not allowed to you"], 403);
        }
        $user_id=$request->user_id;
        $reservations=Reservation::all();
        if($reservations=='[]'){
            return response([ 'message' => "No reservations available"], 404);
        }
        foreach($reservations as $reservation){
            if($reservation->user_id==$user_id && $reservation->trip_id==$trip->id ){
                break;
            }
        }
        if($reservation=='[]'){
            return response([ 'message' => "No reservation available"], 404);
        }
        $trip->freeseats= $trip->freeseats+ $reservation->occupiedSeats;
        $trip->save();
        $reservation->delete();
        return response([ 'message' => "Successfully deleted reservation for $reservation->occupiedSeats seats"]);
    }
    public function editReservation(Trips $trip, Request $request){
        if($trip=='[]'){
            return response([ 'message' => "No trip available"], 404);
        }
        if(auth()->user()->role=="guide" || auth()->user()->role=="guest"){
            return response([ 'message' => "This action is not allowed to you"], 403);
        }
        $user_id=$request->user_id;
        $seats=$request->seats;
        $reservations=Reservation::all();
        if($reservations=='[]'){
            return response([ 'message' => "No reservations available"], 404);
        }
        foreach($reservations as $reservation){
            if($reservation->user_id==$user_id && $reservation->trip_id==$trip->id){
                break;
            }
        }
        if($reservation=='[]'){
            return response([ 'message' => "No reservation available"], 404);
        }
        if($seats>$reservation->occupiedSeats){
            $newSeats=$seats-$reservation->occupiedSeats;
            if($trip->freeseats<$newSeats){
                return response([ 'message' => "Only $trip->freeseats is available and the user has already reserved $reservation->occupiedSeats seats "], 404);
            }
            $trip->freeseats= $trip->freeseats- $newSeats;
        }
        else if($seats<$reservation->occupiedSeats){
            $newSeats=$reservation->occupiedSeats-$seats;
            $trip->freeseats= $trip->freeseats+ $newSeats;
        }
        $trip->save();
        $reservation->occupiedSeats=$seats;
        $reservation->save();

        return response([ 'message' => "Successfully edited reservation for $trip->title"]);
    }
}