<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LikesController extends Controller
{
    public function toggleLike($question_id)
    {
        $user = Auth::user();

        // Check if the question exists
        $questionExists = DB::table('questions')->where('id', $question_id)->exists();
        if (!$questionExists) {
            return response()->json(['success' => false, 'message' => 'Question not found'], 404);
        }

        // Check if the user already liked the question
        $like = DB::table('likes')
            ->where('user_id', $user->id)
            ->where('question_id', $question_id)
            ->first();

        if ($like) {
            // Unlike (Delete the existing like)
            DB::table('likes')
                ->where('user_id', $user->id)
                ->where('question_id', $question_id)
                ->delete();

            $liked = false;
        } else {
            // Like (Insert a new like)
            DB::table('likes')->insert([
                'user_id' => $user->id,
                'question_id' => $question_id,
            ]);

            $liked = true;
        }

        // Get the updated like count
        $likesCount = DB::table('likes')->where('question_id', $question_id)->count();

        return response()->json([
            'success' => true,
            'message' => $liked ? 'Liked successfully' : 'Unliked successfully',
            'liked' => $liked ? 1 : 0,
            'likes_count' => $likesCount
        ]);
    }
    
}
