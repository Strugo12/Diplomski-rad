<?php

namespace App\Http\Controllers;
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
        else{
            return response(['trips' => $trips]);
        }
    }

    public function addTrip(Request $request){
        if(auth()->user()->role=="guest"){
            return response(['message' =>'This action is not allowed to guests']);
        }
        else if(auth()->user()->role=="guide"){
            return response(['message' => 'This action is not allowed to guides']);
        }
        else if(auth()->user()->role=="leader"){
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
        $trip->delete();
        return response([ 'message' => "Trip is deleted!"]);
     }

    public function detail(Trips $trip){

        return response([ 'trip' => $trip]);
    }


}
