<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    protected $apiUrl = 'http://localhost:8001/api/users';

    // Tampilkan daftar pengguna
    public function index()
    {
        $token = session('token');
    
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'http://localhost:8001/api/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);
    
        $data = json_decode($response->getBody(), true);
    
        // Pastikan struktur data yang dikirim sesuai
        $users = $data['data'] ?? [];
    
        return view('user', compact('users'));
    }
    
    // Tambah user baru (POST ke API Golang)
    public function store(Request $request)
    {
        $token = session('token');

        $response = Http::withToken($token)->post($this->apiUrl, [
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => $request->role
        ]);

        if ($response->successful()) {
            return redirect()->back()->with('success', 'User berhasil ditambahkan');
        } else {
            return redirect()->back()->withErrors(['Gagal menambahkan user.']);
        }
    }

    // Update user berdasarkan ID
    public function update(Request $request, $id)
    {
        $token = session('token');

        $response = Http::withToken($token)->put($this->apiUrl . '/' . $id, [
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => $request->role
        ]);

        if ($response->successful()) {
            return redirect()->back()->with('success', 'User berhasil diupdate');
        } else {
            return redirect()->back()->withErrors(['Gagal update user.']);
        }
    }

    // Hapus user berdasarkan ID
    public function destroy($id)
    {
        $token = session('token');

        $response = Http::withToken($token)->delete($this->apiUrl . '/' . $id);

        if ($response->successful()) {
            return redirect()->back()->with('success', 'User berhasil dihapus');
        } else {
            return redirect()->back()->withErrors(['Gagal menghapus user.']);
        }
    }
}
