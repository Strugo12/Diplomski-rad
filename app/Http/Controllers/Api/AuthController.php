<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use phpDocumentor\Reflection\Types\Null_;

class AuthController extends Controller
{
    public function register(Request $request) {

        $fields = $request->validate([
          'name' => 'required|max:55',
          'email' => 'email|required|unique:users',
          'password' => 'required|confirmed',
          'servicePassword'=> 'string',
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
