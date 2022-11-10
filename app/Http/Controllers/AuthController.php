<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        return response()->json(['token' => $user->createToken($request->device_name ?? 'API Token')->plainTextToken], 201);
    }


    public function login(Request $request)
    {
        $body = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:6'
        ]);

        if(!Auth::attempt($body)){
            return response()->json(['message' => 'Details incorrect!'],404); 
        }

        return response()->json(['token' => auth()->user()->createToken($request->device_name ?? 'API Token')->plainTextToken], 200);
    }


    public function logout(){
        auth()->user()->tokens()->delete();

        return response()->json(['message'=>'Logged out!'],200);

    }
}
