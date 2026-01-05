<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MovieRequestController extends Controller
{
    //
    public function request()
    {
        return view('movies.request');
    }
}
