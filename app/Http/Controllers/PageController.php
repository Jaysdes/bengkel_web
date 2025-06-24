<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home() {
        return view('home');
    }

    public function data() {
        return view('data');
    }

    public function teknisi() {
        return view('teknisi');
    }

    public function proses() {
        return view('proses');
    }

    public function laporan() {
        return view('laporan');
    }

    public function serviceCenter() {
        return view('service-center');
    }

    public function user() {
        return view('user');
    }
}
