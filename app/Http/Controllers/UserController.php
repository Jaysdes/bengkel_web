<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected $apiUrl = 'http://localhost:8001/api/users';

    /**
     * Display a listing of users
     * Supports both regular and AJAX requests
     */
    public function index(Request $request)
    {
        $token = session('token');
        $users = [];

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $this->apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
                'timeout' => 10
            ]);

            $data = json_decode($response->getBody(), true);
            $users = $data['data'] ?? [];

        } catch (\Exception $e) {
            Log::error("Failed to fetch users: " . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to load users. Please try again.'
                ], 500);
            }
            
            session()->flash('error', 'Failed to load users. Please check your connection.');
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        }

        return view('user', compact('users'));
    }

    /**
     * Store a newly created user
     * Enhanced with validation and AJAX support
     */
    public function store(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:admin,mekanik,keuangan,gudang,customer'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $token = session('token');

        try {
            $response = Http::withToken($token)
                ->timeout(10)
                ->post($this->apiUrl, [
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => $request->password,
                    'role' => $request->role
                ]);

            if ($response->successful()) {
                $message = 'User berhasil ditambahkan';
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => $message,
                        'data' => $response->json()
                    ]);
                }
                
                return redirect()->back()->with('success', $message);
            } else {
                $errorMessage = 'Gagal menambahkan user';
                $responseData = $response->json();
                
                if (isset($responseData['message'])) {
                    $errorMessage .= ': ' . $responseData['message'];
                }

                if ($request->ajax()) {
                    return response()->json([
                        'error' => $errorMessage
                    ], $response->status());
                }
                
                return redirect()->back()->withErrors([$errorMessage]);
            }

        } catch (\Exception $e) {
            Log::error("Failed to create user: " . $e->getMessage());
            $errorMessage = 'Network error occurred while creating user';

            if ($request->ajax()) {
                return response()->json([
                    'error' => $errorMessage
                ], 500);
            }
            
            return redirect()->back()->withErrors([$errorMessage]);
        }
    }

    /**
     * Update the specified user
     * Enhanced with validation and AJAX support
     */
    public function update(Request $request, $id)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:6',
            'role' => 'required|string|in:admin,mekanik,keuangan,gudang,customer'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $token = session('token');
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role
        ];

        // Only include password if provided
        if ($request->filled('password')) {
            $updateData['password'] = $request->password;
        }

        try {
            $response = Http::withToken($token)
                ->timeout(10)
                ->put($this->apiUrl . '/' . $id, $updateData);

            if ($response->successful()) {
                $message = 'User berhasil diupdate';
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => $message,
                        'data' => $response->json()
                    ]);
                }
                
                return redirect()->back()->with('success', $message);
            } else {
                $errorMessage = 'Gagal mengupdate user';
                $responseData = $response->json();
                
                if (isset($responseData['message'])) {
                    $errorMessage .= ': ' . $responseData['message'];
                }

                if ($request->ajax()) {
                    return response()->json([
                        'error' => $errorMessage
                    ], $response->status());
                }
                
                return redirect()->back()->withErrors([$errorMessage]);
            }

        } catch (\Exception $e) {
            Log::error("Failed to update user: " . $e->getMessage());
            $errorMessage = 'Network error occurred while updating user';

            if ($request->ajax()) {
                return response()->json([
                    'error' => $errorMessage
                ], 500);
            }
            
            return redirect()->back()->withErrors([$errorMessage]);
        }
    }

    /**
     * Remove the specified user
     * Enhanced with AJAX support
     */
    public function destroy(Request $request, $id)
    {
        $token = session('token');

        try {
            $response = Http::withToken($token)
                ->timeout(10)
                ->delete($this->apiUrl . '/' . $id);

            if ($response->successful()) {
                $message = 'User berhasil dihapus';
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => $message
                    ]);
                }
                
                return redirect()->back()->with('success', $message);
            } else {
                $errorMessage = 'Gagal menghapus user';
                $responseData = $response->json();
                
                if (isset($responseData['message'])) {
                    $errorMessage .= ': ' . $responseData['message'];
                }

                if ($request->ajax()) {
                    return response()->json([
                        'error' => $errorMessage
                    ], $response->status());
                }
                
                return redirect()->back()->withErrors([$errorMessage]);
            }

        } catch (\Exception $e) {
            Log::error("Failed to delete user: " . $e->getMessage());
            $errorMessage = 'Network error occurred while deleting user';

            if ($request->ajax()) {
                return response()->json([
                    'error' => $errorMessage
                ], 500);
            }
            
            return redirect()->back()->withErrors([$errorMessage]);
        }
    }
}
