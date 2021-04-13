<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelloController extends Controller
{
    // Test Hello World

    public function index(Request $request) {
        return response()->json(['action' => True, 'message' => 'Hello World!']);
    }
}
