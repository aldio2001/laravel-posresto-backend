<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    //  //index Api product
    public function index()
    {
        //get all product
        $categories = Category::all();

        $products = Category::paginate(10);
        return response()->json([
            'status' => 'success',
            'data' => $categories
        ],200);
    }

}

