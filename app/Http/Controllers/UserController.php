<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::check() && Auth::user()->role == 'admin'){
            $user = User::all();
            return response()->json(['result'=> $user]);
        }
        return response()->json(['message'=>"You don't have admin access."]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Auth::check() && Auth::user()->role =='admin'){
            $request->validate([
                'name'=>'required|string',
                'email'=>'required|string|unique:users,email',
                'password' => 'required|string|confirmed',
                'role' => 'required|string',
            ]);

            $data = $request->all();
            $data['password'] = bcrypt($request->password);
            $user = User::create($data);

            $token = $user->createToken('usertoken')->plainTextToken;

            $response = [
                'user'=>$user,
                'token'=>$token,
            ];

            return response($response, 201);
        }
        return response()->json(['message'=>"Don't have admin access."]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(Auth::check() && Auth::user()->role =='admin'){
            $user = User::find($id);
            return $user;
        }
        return response()->json(['message'=>"Don't have admin access."]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(Auth::check() && Auth::user()->role =='admin'){
            $request->validate([
                'role' => 'required|string',
            ]);
            $user = User::find($id);
            $user->role = $request->role;
            $user->update();
    
            return response()->json(['success' => true, 'message' => 'User Role updated successfully!', 
                'updated_data' => $user], 200);
        }

        return response()->json(['message'=>"Don't have admin access."]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Auth::check() && Auth::user()->role =='admin'){
            $user = User::find($id);
            $user->jobs()->delete();
            
            User::destroy($id);
            return response()->json(['success' => true, 'message' => 'User deleted successfully!'], 200);
        }
        return response()->json(['message'=>"Don't have admin access."]);
    }

    // registration
    public function register(Request $request)
    {
        $request->validate([
            'name'=>'required|string',
            'email'=>'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
        ]);
        
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        $token = $user->createToken('usertoken')->plainTextToken;

        $response = [
            'user'=>$user,
            'token'=>$token
        ];

        return response($response, 201);
    }
    
    // Login
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'=>'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('email',$data['email'])->first();

        if(!$user || !Hash::check($data['password'], $user->password)){
            return response(['message'=>'Wrong Credentials'],401);
        }

        $token = $user->createToken('usertoken')->plainTextToken;

        $response = [
            'user'=>$user,
            'token'=>$token
        ];

        return response($response, 201);
    }

    //Logout
    public function logout(Request $request,$id)
    {
        //when logout token delete 
        $user = User::find($id);
        $logout = $user->tokens()->delete();

        if($logout){
            return response()->json(['message'=>'Logged Out..'],201);
        }

    }
}
