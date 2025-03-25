<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Models\questions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MyProfileController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'email' => 'required|email'
            ]);

            // Fetch user by email
            $user = User::where('email', $request->email)
                ->select('id', 'name', 'email')
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            // Fetch uploaded posts using Question model
            $uploadedPosts = questions::where('user_id', $user->id)
                ->select('id', 'title') // Adjust fields as per your questions table
                ->get()
                ->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'title' => $question->title
                    ];
                })->toArray();

            // Fetch saved posts using DB facade
            // Get question_ids from saved_questions pivot table
            $savedQuestionIds = DB::table('savepost')
                ->where('user_id', $user->id)
                ->pluck('question_id')
                ->toArray();

            // Fetch questions matching those question_ids
            $savedPosts = DB::table('questions')
                ->whereIn('id', $savedQuestionIds)
                ->select('id', 'title') // Adjust fields as per your questions table
                ->get()
                ->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'title' => $question->title
                    ];
                })->toArray();

            // Prepare the response data
            $responseData = [
                'status' => 'success',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'uploaded_posts' => $uploadedPosts,
                    'saved_posts' => $savedPosts
                ]
            ];

            return response()->json($responseData, 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }
}
