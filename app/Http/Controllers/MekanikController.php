<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MekanikController extends Controller
{
    public function index()
    {
        $client = new \GuzzleHttp\Client();
        $token = session('token');
    
        $res = $client->request('GET', 'http://localhost:8000/api/mekanik', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ]
        ]);
    
        $data = json_decode($res->getBody()->getContents(), true);
    
        return view('mekanik', [
            'dataMekanik' => $data['data'] ?? []
        ]);
    }
    
}
