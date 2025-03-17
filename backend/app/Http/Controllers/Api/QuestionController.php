<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\questions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    public function getAllQuestions(Request $request)
    {
        $email = $request->email;
    
        // ✅ Find user by email
        $user = User::where('email', $email)->first();
    
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        $userId = $user->id;
    
        // Fetch all questions with the user who posted them
        $questions = questions::with('user:id,name')->get();
    
        // Fetch likes count for each question
        $likes = DB::table('likes')
            ->select('question_id', DB::raw('COUNT(*) as likes_count'))
            ->groupBy('question_id')
            ->get()
            ->keyBy('question_id'); // Store likes as an associative array for quick lookup
    
        // Fetch which questions the user has liked
        $userLikes = DB::table('likes')
            ->where('user_id', $userId)
            ->pluck('question_id') // ✅ Extract only question_id values
            ->toArray();
    
        // Attach likes data to each question
        foreach ($questions as $question) {
            $question->likes_count = $likes[$question->id]->likes_count ?? 0; // Assign likes count
            $question->liked_by_user = in_array($question->id, $userLikes); 
        }
    
        return response()->json([
            'success' => true,
            'questions' => $questions, // Ensure it's an array of questions
            'userLikes' => $userLikes,
            'user_id' => $userId,
            'email' => $email
        ], 200);
    }
    



    public function getQuestionById(Request $request, $id)
{
    // Find the question by ID
    $question = questions::find($id);

    if (!$question) {
        return response()->json([
            'success' => false,
            'message' => 'Question not found'
        ], 404);
    }

    // Get the email from the query parameters
    $email = $request->query('email');
    $user = $email ? User::where('email', $email)->first() : null;

    // If user not found, proceed anyway (optional: return error if user is required)
    if (!$user && $email) {
        return response()->json([
            'success' => false,
            'message' => 'User not found'
        ], 404);
    }

    $userId = $user ? $user->id : null;

    // Determine if the user liked this specific question
    $isUserLiked = $userId && $question->likes()
        ->where('user_id', $userId)
        ->exists();

    // Prepare the question data with additional details
    $questionData = [
        'id' => $question->id,
        'title' => $question->title,
        'is_anonymous' => $question->is_anonymous,
        'created_at' => $question->created_at->toDateTimeString(),
        'likes_count' => $question->likes()->count(),
        // 'replies_count' => $question->replies()->count(), // Ensure replies() is defined if used
        'liked_by_user' => $isUserLiked,
        'user' => $question->is_anonymous ? null : [
            'name' => $question->user ? $question->user->name : 'Unknown',
        ],
    ];

    return response()->json([
        'success' => true,
        'question' => $questionData
    ], 200);
}

    public function getQuestionsByCategory(Request $request, $categoryId)
    {
        $email = $request->email;
    
        // ✅ Find user by email
        $user = User::where('email', $email)->first();
    
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        $userId = $user->id;
    
        // Fetch questions by category with user data, paginated
        $questions = Questions::with('user:id,name')
            ->where('category_id', $categoryId)
            ->paginate(40);
    
        // Check if no questions are found
        if ($questions->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No questions found for this category'
            ], 404);
        }
    
        // Fetch likes count for each question in this category
        $likes = DB::table('likes')
            ->select('question_id', DB::raw('COUNT(*) as likes_count'))
            ->whereIn('question_id', $questions->pluck('id')) // Only fetch likes for these questions
            ->groupBy('question_id')
            ->get()
            ->keyBy('question_id'); // Store as associative array for quick lookup
    
        // Fetch which questions the user has liked
        $userLikes = DB::table('likes')
            ->where('user_id', $userId)
            ->whereIn('question_id', $questions->pluck('id')) // Limit to current page questions
            ->pluck('question_id')
            ->toArray();
    
        // Attach likes data to each question
        foreach ($questions as $question) {
            $question->likes_count = $likes[$question->id]->likes_count ?? 0; // Assign likes count
            $question->liked_by_user = in_array($question->id, $userLikes);  // Check if user liked
        }
    
        return response()->json([
            'success' => true,
            'questions' => $questions->items(), // Only return the items (questions)
            'pagination' => [
                'current_page' => $questions->currentPage(),
                'last_page' => $questions->lastPage(),
                'total' => $questions->total(),
            ],
            'userLikes' => $userLikes,
            'user_id' => $userId,
            'email' => $email
        ], 200);
    }




    
    public function store(Request $request){
        // Validate incoming request (No need to validate `user_id`)
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'category_id' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
    
        // ✅ Get authenticated user's ID
        $user_id = Auth::id();
    
        if (!$user_id) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }
        $isAnonymous = $request->has('is_anonymous') ? $request->is_anonymous : 0;

        // ✅ Create new question
        $question = questions::create([
            'title' => $request->title,
            'user_id' => $user_id, 
            'category_id' => $request->category_id,
            'is_anonymous' => $isAnonymous
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Question created successfully',
            'question' => $question
        ], 201);
    }

}
