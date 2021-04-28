<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Trips;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function reserve(Request $request, Trips $trip){
        $fields = $request->validate([
            'seats' => 'required|max:8',
          ]);
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
            return response([ 'message' => "Trip does not exist"]);
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
}
