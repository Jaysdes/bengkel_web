<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        $baseUrl = 'http://localhost:8001/api';

        // Ambil data dari API Golang
        $usersResponse = Http::get("$baseUrl/users");
        $transaksiResponse = Http::get("$baseUrl/transaksi");
        $spkResponse = Http::get("$baseUrl/spk");
        $prosesResponse = Http::get("$baseUrl/proses"); // Tambahan proses

        // Ambil hanya bagian 'data'
        $users = $usersResponse->json('data') ?? [];
        $transaksis = $transaksiResponse->json('data') ?? [];
        $spks = $spkResponse->json('data') ?? [];
        $prosesList = $prosesResponse->json('data') ?? [];

        // Hitung total
        $userCount = count($users);
        $transaksiCount = count($transaksis);
        $spkCount = count($spks);
        $prosesCount = count($prosesList);

        // Dummy data untuk grafik
        $chartLabels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum'];
        $chartData = [1, 2, 3, 2, 5];

        return view('dashboard', compact(
            'userCount',
            'transaksiCount',
            'spkCount',
            'prosesCount',   // <-- Tambahkan ini
            'chartLabels',
            'chartData'
        ));
    }
}
