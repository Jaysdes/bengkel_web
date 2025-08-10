<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotaController extends Controller
{
    public function nota($id)
    {
        $response = Http::withToken(session('token'))
            ->get(env('API_URL')."/transaksi/$id");
    
        $data = $response->json();
    
        if (isset($data['data'])) {
            $transaksi = $data['data'];
    
            // Jika tidak ada created_at, buat manual
            if (!isset($transaksi['created_at'])) {
                $transaksi['created_at'] = now()->toDateTimeString();
            }
    
            return view('nota', ['transaksi' => $transaksi]);
        }
    
        abort(404, 'Transaksi tidak ditemukan');
    }
    
}
