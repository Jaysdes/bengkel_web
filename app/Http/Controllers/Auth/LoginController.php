<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    // Menampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses login ke API Golang
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->post('http://localhost:8001/auth/login', [
                'json' => $credentials
            ]);

            $data = json_decode($response->getBody(), true);

            // Simpan session: token, user, dan flag success
            session([
                'token'   => $data['data']['token'],
                'user'    => $data['data']['user'],
                'success' => true
            ]);

            return redirect()->route('dashboard')->with('success', 'Login berhasil!');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Jika respon 401/400 dari API
            $body = json_decode($e->getResponse()->getBody(), true);
            $message = $body['message'] ?? 'Login gagal.';

            return redirect()->back()->with('error', $message)->withInput();
        } catch (\Exception $e) {
            // Error lainnya
            return redirect()->back()->with('error', 'Login gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function logout(Request $request)
    {
        // Hapus semua data session
        $request->session()->flush();

        // Redirect ke halaman login
        return redirect()->route('login');
    }
}
