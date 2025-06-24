<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SCController extends Controller
{
    public function index()
    {
        return view('service-center');
    }
}
