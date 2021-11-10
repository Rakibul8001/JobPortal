<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //user collections
    public function index(){
        if(Auth::check() && Auth::user()->role == 'admin'){
            $user = User::all();
            return response()->json(['result'=> $user]);
        }
        return response()->json(['message'=>"You don't have admin access."]);

    }
    //User registration
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'=>'required|string',
            'email'=>'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
            'role' => 'required|string'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => $data['role'],
        ]);

        $token = $user->createToken('usertoken')->plainTextToken;

        $response = [
            'user'=>$user,
            'token'=>$token
        ];

        return response($response, 201);
    }

        //User Login
        public function login(Request $request)
        {
            $data = $request->validate([
                'email'=>'required|string',
                'password' => 'required|string'
            ]);
    
            $user = User::where('email',$data['email'])->first();
    
            if(!$user || !Hash::check($data['password'], $user->password)){
                return response([
                    'message'=>'Wrong Credentials'
                ],401);
            }
    
            $token = $user->createToken('usertoken')->plainTextToken;
    
            $response = [
                'user'=>$user,
                'token'=>$token
            ];
    
            return response($response, 201);
        }

        //Logout
        public function logout(Request $request, $id)
        {
            $user = User::find($id);
            $logout = $user->tokens()->delete();

            if($logout)
            {
                return response()->json(['message'=>'Logged Out..']);
            }
        }

    
}
