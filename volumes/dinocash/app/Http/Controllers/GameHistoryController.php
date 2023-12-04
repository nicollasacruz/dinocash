<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class GameHistoryController extends Controller
{
    public function user(Request $request)
    {
        return Inertia::render('User/History');
    }
}
