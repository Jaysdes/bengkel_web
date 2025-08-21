<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with optional date filtering
     * Supports query parameters: start_date and end_date for data filtering
     */
    public function index(Request $request)
    {
        $baseUrl = 'https://apibengkel.up.railway.app/api';
        
        // Get date filter parameters from query string
        $startDate = $request->query('start_date', date('Y-m-d', strtotime('-7 days')));
        $endDate = $request->query('end_date', date('Y-m-d'));

        // Initialize default values
        $users = [];
        $transaksis = [];
        $spks = [];
        $prosesList = [];

        try {
            // Prepare query parameters for API calls
            $queryParams = [
                'start_date' => $startDate,
                'end_date' => $endDate
            ];

            // Make API calls with date filtering
            $usersResponse = Http::timeout(10)->get("$baseUrl/users", $queryParams);
            $transaksiResponse = Http::timeout(10)->get("$baseUrl/transaksi", $queryParams);
            $spkResponse = Http::timeout(10)->get("$baseUrl/spk", $queryParams);
            $prosesResponse = Http::timeout(10)->get("$baseUrl/proses", $queryParams);

            // Extract data from responses with fallback to empty arrays
            $users = data_get($usersResponse->json(), 'data', []) ?? [];
            $transaksis = data_get($transaksiResponse->json(), 'data', []) ?? [];
            $spks = data_get($spkResponse->json(), 'data', []) ?? [];
            $prosesList = data_get($prosesResponse->json(), 'data', []) ?? [];

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("Dashboard API call failed: " . $e->getMessage(), [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'base_url' => $baseUrl
            ]);

            // Set flash message for user notification
            session()->flash('warning', 'Some data may not be available due to API connectivity issues.');
        }

        // Calculate counts
        $userCount = count($users);
        $transaksiCount = count($transaksis);
        $spkCount = count($spks);
        $prosesCount = count($prosesList);

        // Generate chart data based on filtered transactions
        $chartLabels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum'];
        $chartData = $this->generateChartData($transaksis, $startDate, $endDate);

        return view('dashboard', compact(
            'userCount',
            'transaksiCount',
            'spkCount',
            'prosesCount',
            'chartLabels',
            'chartData',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Generate chart data based on filtered transactions
     */
    private function generateChartData($transaksis, $startDate, $endDate)
    {
        // Simple implementation - can be enhanced based on actual data structure
        if (empty($transaksis)) {
            return [1, 2, 3, 2, 5]; // Default dummy data
        }

        // Generate data based on transaction dates within the filter range
        $chartData = [];
        for ($i = 0; $i < 5; $i++) {
            $chartData[] = rand(1, 10); // Placeholder - replace with actual logic
        }

        return $chartData;
    }
}
