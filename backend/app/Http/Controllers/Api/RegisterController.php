<?php

namespace App\Http\Controllers\Api;

use session;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;


class RegisterController extends Controller
{

    public function index(){
        $users = User::all();
        return view('index',compact('users'));
    }
    Public function getAllUsers(){
        $users = User::all();
        return view('index',compact('users'));
    }

    // public function register(Request $request)
    // {

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password), 
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'User registered successfully',
    //         'user' => $user
    //     ], 201);
    // }
    // public function showLogin(){
    //     return view('login');
    // }


//   public function userLogin(Request $request){
//     $credentials = $request->validate([
//         'email' => 'required|email',
//         'password' => 'required',
//     ]);

//     if (!Auth::attempt($credentials)) {
//         return response()->json(['message' => 'Invalid credentials'], 401);
//     }

//     $user = Auth::user();
//     // $token = $user->createToken('auth_token')->plainTextToken; 

//     return response()->json([
//         'message' => 'Login successful',
//         'token' => $token,  
//         'user' => $user,
//     ]);
// }


public function userLogout(Request $request)
{
    try {
        if ($request->user()) {
            // Revoke all user tokens (logout from all devices)
            $request->user()->tokens()->delete(); 

            return response()->json([
                'message' => 'Logout successful'
            ], 200);
        }

        return response()->json([
            'message' => 'User not authenticated'
        ], 401);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Logout faileddd',
            'error' => $e->getMessage()
        ], 500);
    }
}
    

}
