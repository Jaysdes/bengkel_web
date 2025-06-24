<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SP_Controller extends Controller
{
    public function index()
    {
        return view('sparepart');
    }
}
