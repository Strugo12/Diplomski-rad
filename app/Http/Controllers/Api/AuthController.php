<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ChangePassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use phpDocumentor\Reflection\Types\Null_;

class AuthController extends Controller
{
    public function register(Request $request) {

        $fields = $request->validate([
          'name' => 'required|max:55',
          'email' => 'email|required|unique:users',
          'password' => 'required|confirmed',
          'servicePassword'=> 'max:55',
        ]);

        $fields['password'] = bcrypt($request->password);
        if($fields['servicePassword']=='8721'){
            $user = User::create([
                'name' => $fields['name'],
                'email' => $fields['email'],
                'role'=> 'leader',
                'password' => $fields['password']
            ]);
        }
        else if($fields['servicePassword']=='4382'){
            $user = User::create([
                'name' => $fields['name'],
                'email' => $fields['email'],
                'role'=> 'guide',
                'password' => $fields['password']
            ]);
        }
        else{
            $user = User::create([
                'name' => $fields['name'],
                'email' => $fields['email'],
                'role'=> 'guest',
                'password' => $fields['password']
            ]);
        }
        $token = $user->createToken('token')->accessToken;
        $user->api_token=$token;
        $user->save();
        return response([ 'user' => $user, 'token' => $token]);
      }
      public function changePassword(Request $request) {
        $fields = $request->validate([
            'code' => 'required|string',
            'password'=> 'required|string',
            'repeatPassword'=> 'required|string'
        ]);
        if($fields['password']!= $fields['repeatPassword']){
            return response([ 'message' => "Passwords must match"]);
        }
        $fields['password'] = bcrypt($request->password);
        $user=User::where('remember_token',$fields['code'] )->firstOrFail();
        $user->password=$fields['password'];
        $user->remember_token=null;
        $user->save();
        return response([ 'message' => "Passwords changed"]);
      }

      public function forgotPassword(Request $request) {
        $fields = $request->validate([
            'email' => 'required|string',
        ]);
        $user=User::where('email',$fields['email'] )->firstOrFail();;
        $details=mt_rand(1000, 9999);
        $user->remember_token=$details;
        $user->save();
        Mail::to($fields['email'])->send(new ChangePassword($details));
        return response(['message' => $details]);

      }
      public function login(Request $request) {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);
        if (!auth()->attempt($fields)) {
            return response(['message' => 'Wrong email adress or password!']);
          }
          $token = auth()->user()->createToken('token')->accessToken;
          $user=auth()->user();
          $user->api_token=$token;
          $user->save();
        return response(['user' => auth()->user(), 'token' => $token]);
      }

      public function logout(Request $request) {
        $user=auth()->user();
          $user->api_token=Null;
          $user->save();
        return [
            'message' => 'Logged out'
        ];
    }

    public function details(){
        return response()->json(['user' => auth()->user()], 200);
    }
}
