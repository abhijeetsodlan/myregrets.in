<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SavePostController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'question_id' => 'required',
                'email' => 'required|email'
            ]);

            // Get user by email
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            // Check if already saved
            $existingSave = DB::table('savepost')
                ->where('user_id', $user->id)
                ->where('question_id', $request->question_id)
                ->exists();

                if ($existingSave) {
                    // If it exists, delete it (unsave)
                    DB::table('savepost')
                        ->where('user_id', $user->id)
                        ->where('question_id', $request->question_id)
                        ->delete();
        
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Post unsaved successfully'
                    ], 200);
                }else{

            // Save the post
            DB::table('savepost')->insert([
                'user_id' => $user->id,
                'question_id' => $request->question_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Post saved successfully'
            ], 201);
        }

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
}
