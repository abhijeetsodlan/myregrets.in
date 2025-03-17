<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocioliteController extends Controller
{
    public function googleLogin(){
        return Socialite::driver('google')->redirect();
    }

    public function googleAuthData()
    {
        try {
            // Disable SSL verification (for local development; avoid in production)
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
                ->user();
    
            // Find user by google_id only
            $user = User::where('googleId', $googleUser->id)->first();
    
            if (!$user) {
                // Create a new user (Google login only, no email check)
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => Hash::make('Password@1234'), 
                    'googleId' => $googleUser->id,
                ]);
            }
    
            // Log in the user
            Auth::login($user);
    
            // Generate a token for API authentication
            $token = $user->createToken('auth_token')->plainTextToken;
            return redirect()->away(env('FRONTEND_URL', 'http://localhost:5173/questions') . "/auth-success?token=$token&name=" . urlencode($user->name) . "&email=" . urlencode($user->email));
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Get the authenticated user via Sanctum
            $user = $request->user(); // or Auth::user()

            if (!$user) {
                return response()->json([
                    'message' => 'No authenticated user found',
                ], 401);
            }

            // Revoke the current token (the one used in this request)
            $request->user()->currentAccessToken()->delete();

            // Optionally revoke all tokens for the user
            // $user->tokens()->delete();

            return response()->json([
                'message' => 'Successfully logged out',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Logout failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
