<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotaController extends Controller
{
    public function nota($id)
    {
        // Ambil token dari session (pastikan sudah diset waktu login)
        $token = session('token');

        // Panggil API Golang transaksi
        $response = Http::withToken($token)
            ->get("https://apibengkel.up.railway.app/api/transaksi/{$id}");

        if ($response->failed()) {
            abort(404, 'Data transaksi tidak ditemukan.');
        }

        $data = $response->json();

        // Ambil data utama dari response
        $transaksi = $data['data'] ?? null;

        if (!$transaksi) {
            abort(404, 'Data transaksi tidak ditemukan.');
        }

        // Kirim ke blade
        return view('nota', [
            'transaksi' => $transaksi,
            'statusPembayaran' => $transaksi['status_pembayaran'] ?? 'Belum Lunas',
        ]);
    }
}
