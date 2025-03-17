<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = DB::table('category')->select('id', 'name')->get();
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}
