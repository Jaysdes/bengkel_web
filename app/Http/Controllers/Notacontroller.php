<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotaController extends Controller
{
    protected $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('API_URL', 'http://localhost:8001/api');
    }

    public function nota($id)
    {
        try {
            // Get transaction details
            $transaksiResponse = Http::get("{$this->apiUrl}/transaksi/{$id}");
            
            if (!$transaksiResponse->successful()) {
                abort(404, 'Transaksi tidak ditemukan');
            }

            $transaksi = $transaksiResponse->json('data');

            // Get related data
            $customer = $this->getCustomerData($transaksi['id_customer']);
            $mekanik = $this->getMekanikData($transaksi['id_mekanik']);
            $jenis = $this->getJenisKendaraanData($transaksi['id_jenis']);
            $spk = $this->getSPKData($transaksi['id_spk']);
            $detailTransaksi = $this->getDetailTransaksi($transaksi['id_transaksi']);

            // Enrich transaction data
            $transaksi['customer'] = $customer;
            $transaksi['mekanik'] = $mekanik;
            $transaksi['jenis_kendaraan'] = $jenis;
            $transaksi['spk'] = $spk;
            $transaksi['detail_transaksi'] = $detailTransaksi;
            $transaksi['tanggal'] = date('d-m-Y');
            $transaksi['waktu'] = date('H:i:s');

            // Calculate totals and breakdown
            $breakdown = $this->calculateBreakdown($transaksi, $detailTransaksi);
            $transaksi = array_merge($transaksi, $breakdown);

            return view('nota', ['transaksi' => $transaksi]);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memuat nota: ' . $e->getMessage());
        }
    }

    private function getCustomerData($id)
    {
        try {
            $response = Http::get("{$this->apiUrl}/customers/{$id}");
            return $response->successful() ? $response->json('data') : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getMekanikData($id)
    {
        try {
            $response = Http::get("{$this->apiUrl}/mekanik/{$id}");
            return $response->successful() ? $response->json('data') : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getJenisKendaraanData($id)
    {
        try {
            $response = Http::get("{$this->apiUrl}/jenis_kendaraan/{$id}");
            return $response->successful() ? $response->json('data') : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getSPKData($id)
    {
        try {
            $response = Http::get("{$this->apiUrl}/spk/{$id}");
            return $response->successful() ? $response->json('data') : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getDetailTransaksi($id)
    {
        try {
            $response = Http::get("{$this->apiUrl}/detail_transaksi");
            if ($response->successful()) {
                $allDetails = $response->json('data');
                return array_filter($allDetails, function($detail) use ($id) {
                    return isset($detail['id_transaksi']) && $detail['id_transaksi'] == $id;
                });
            }
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    private function calculateBreakdown($transaksi, $detailTransaksi)
    {
        $breakdown = [
            'subtotal_jasa' => $transaksi['harga_jasa'] ?? 0,
            'subtotal_sparepart' => $transaksi['harga_sparepart'] ?? 0,
            'items' => [],
            'total_items' => 0,
            'grand_total' => $transaksi['total'] ?? 0
        ];

        // Add service item
        if ($breakdown['subtotal_jasa'] > 0) {
            $breakdown['items'][] = [
                'type' => 'jasa',
                'nama' => 'Jasa Service',
                'qty' => 1,
                'harga' => $breakdown['subtotal_jasa'],
                'subtotal' => $breakdown['subtotal_jasa']
            ];
            $breakdown['total_items']++;
        }

        // Add sparepart items
        foreach ($detailTransaksi as $detail) {
            $sparepart = $this->getSparepartData($detail['id_sparepart']);
            $breakdown['items'][] = [
                'type' => 'sparepart',
                'nama' => $sparepart['nama_sparepart'] ?? 'Sparepart #' . $detail['id_sparepart'],
                'qty' => $detail['qty'] ?? 1,
                'harga' => $sparepart['harga_jual'] ?? 0,
                'subtotal' => $detail['total'] ?? 0
            ];
            $breakdown['total_items']++;
        }

        return $breakdown;
    }

    private function getSparepartData($id)
    {
        try {
            $response = Http::get("{$this->apiUrl}/sparepart/{$id}");
            return $response->successful() ? $response->json('data') : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function printNota($id)
    {
        // Same as nota but with print layout
        $transaksi = $this->nota($id);
        return view('nota-print', $transaksi->getData());
    }
} 