<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TeknisiController extends Controller
{
    protected string $apiBase;

    public function __construct()
    {
        $this->apiBase = "https://apibengkel.up.railway.app/api";
    }

    /**
     * Halaman teknisi (daftar SPK + dropdown mekanik, jasa, service, customer).
     * Keluhan ikut dipass ke view agar bisa ditampilkan di card keterangan.
     */
    public function index(Request $request)
    {
        $token = session('token');

        // Ambil semua SPK
        $spkResponse = Http::withToken($token)->get("{$this->apiBase}/spk");
        $spks = $spkResponse->ok() ? Arr::get($spkResponse->json(), 'data', []) : [];

        // Ambil data relasi
        $jasaResponse     = Http::withToken($token)->get("{$this->apiBase}/jenis_jasa");
        $serviceResponse  = Http::withToken($token)->get("{$this->apiBase}/jenis_service");
        $customerResponse = Http::withToken($token)->get("{$this->apiBase}/customers");
        $mekanikResponse  = Http::withToken($token)->get("{$this->apiBase}/mekanik");

        $jasaMap         = $jasaResponse->ok()
            ? collect($jasaResponse->json()['data'])->pluck('nama_jasa', 'id_jasa')
            : collect();
        $serviceMap      = $serviceResponse->ok()
            ? collect($serviceResponse->json()['data'])->pluck('jenis_service', 'id_service')
            : collect();
        $customerMap     = $customerResponse->ok()
            ? collect($customerResponse->json()['data'])->pluck('nama_customer', 'id_customer')
            : collect();
        $customerPhoneMap = $customerResponse->ok()
            ? collect($customerResponse->json()['data'])->pluck('telepon', 'id_customer')
            : collect();

        $mekanikList = $mekanikResponse->ok() ? $mekanikResponse->json()['data'] : [];
        $mekanikPhoneMap = $mekanikResponse->ok()
            ? collect($mekanikResponse->json()['data'])->pluck('telepon', 'id_mekanik')
            : collect();

        // Tambahkan relasi + keluhan ke setiap SPK (untuk card keterangan)
        $spks = collect($spks)->map(function ($spk) use ($jasaMap, $serviceMap, $customerMap, $customerPhoneMap) {
            $spk['jasa']            = $jasaMap[$spk['id_jasa']]       ?? ($spk['jasa'] ?? null);
            $spk['service']         = $serviceMap[$spk['id_service']] ?? ($spk['service'] ?? null);
            $spk['customer']        = $customerMap[$spk['id_customer']] ?? ($spk['customer'] ?? null);
            $spk['telp_customer']   = $customerPhoneMap[$spk['id_customer']] ?? ($spk['telp_customer'] ?? null);
            $spk['keluhan']         = $spk['keluhan'] ?? null; // penting untuk card keluhan
            $spk['jenis_kendaraan'] = $spk['id_jenis'] == 1 ? 'Motor' : ($spk['id_jenis'] == 2 ? 'Mobil' : null);
            return $spk;
        })->toArray();

        // Kirim semua data ke view teknisi
        return view('teknisi', compact('spks', 'mekanikList', 'mekanikPhoneMap'));
    }

    /**
     * Halaman transaksi: pastikan tabel di transaksi.blade selalu terupdate.
     */
    public function transaksiIndex(Request $request)
    {
        $token = session('token');

        $trxResponse = Http::withToken($token)->get("{$this->apiBase}/transaksi");
        $transaksis = $trxResponse->ok() ? Arr::get($trxResponse->json(), 'data', []) : [];

        return view('transaksi', compact('transaksis'));
    }

    /**
     * SATU tombol submit → JALANKAN 2 LOGIKA:
     * 1) Update SPK (tanpa harga; status → proses/di proses mekanik)
     * 2) Create Transaksi (mekanik, telp, jasa+harga, sparepart+harga, total)
     *
     * Request body (AJAX JSON dari blade):
     * - id_spk (required)
     * - nama_customer, no_kendaraan, keluhan (untuk SPK)
     * - jasa: [{id_jasa, nama, harga}, ...]
     * - sparepart: [{id_sparepart, nama, qty, harga, subtotal}, ...]
     * - id_mekanik, telp_mekanik
     */
    public function storePengerjaan(Request $request)
    {
        $token = session('token');

        // Ambil dan normalize payload
        $idSpk         = $request->input('id_spk');
        $namaCustomer  = $request->input('nama_customer');
        $noKendaraan   = $request->input('no_kendaraan');
        $keluhan       = $request->input('keluhan');

        $idMekanik     = $request->input('id_mekanik');
        $telpMekanik   = $request->input('telp_mekanik');

        $jasa          = $this->asArray($request->input('jasa', []));       // array of {id_jasa, nama, harga}
        $sparepart     = $this->asArray($request->input('sparepart', []));  // array of {id_sparepart, nama, qty, harga, subtotal}

        if (!$idSpk) {
            return response()->json(['message' => 'id_spk wajib diisi'], 422);
        }

        // Hitung total
        $totalJasa      = collect($jasa)->sum(function ($i) { return (int) ($i['harga'] ?? 0); });
        $totalSparepart = collect($sparepart)->sum(function ($i) { return (int) ($i['subtotal'] ?? ((int)($i['harga'] ?? 0) * (int)($i['qty'] ?? 0))); });
        $totalBiaya     = $totalJasa + $totalSparepart;

        // --- 1) Update SPK (HANYA field dasar + list nama jasa tanpa harga) ---
        $spkPayload = [
            'id_spk'       => $idSpk,
            'customer'     => $namaCustomer,
            'no_kendaraan' => $noKendaraan,
            'keluhan'      => $keluhan,
            'jenis_jasa'   => collect($jasa)->pluck('nama')->values()->all(), // TANPA harga
            'status'       => 'proses', // "di proses mekanik"
        ];

        try {
            $spkRes = Http::withToken($token)->put("{$this->apiBase}/spk/{$idSpk}", $spkPayload);

            if (!$spkRes->ok()) {
                $err = $spkRes->json();
                return response()->json([
                    'message' => 'Gagal update SPK',
                    'error'   => $err
                ], $spkRes->status() ?: 400);
            }

            // --- 2) Insert Transaksi (detail lengkap dengan harga) ---
            $transaksiPayload = [
                'id_spk'       => $idSpk,
                'id_mekanik'   => $idMekanik,
                'telp_mekanik' => $telpMekanik,
                'jasa'         => array_values($jasa),
                'sparepart'    => array_values($sparepart),
                'total_biaya'  => $totalBiaya,
            ];

            $trxRes = Http::withToken($token)->post("{$this->apiBase}/transaksi", $transaksiPayload);

            if (!$trxRes->ok()) {
                // Attempt rollback status SPK → "Belum" bila transaksi gagal
                try {
                    Http::withToken($token)->put("{$this->apiBase}/spk/{$idSpk}", array_merge($spkPayload, ['status' => 'Belum']));
                } catch (Exception $e) {
                    Log::warning('Rollback SPK gagal: '.$e->getMessage());
                }

                return response()->json([
                    'message' => 'Gagal simpan transaksi',
                    'error'   => $trxRes->json()
                ], $trxRes->status() ?: 400);
            }

            // Ambil data terbaru untuk refresh tabel di UI (opsional)
            $latestSpk = Http::withToken($token)->get("{$this->apiBase}/spk");
            $latestTrx = Http::withToken($token)->get("{$this->apiBase}/transaksi");

            return response()->json([
                'message'       => 'SPK & Transaksi berhasil disimpan',
                'spk_updated'   => $spkRes->json(),
                'trx_created'   => $trxRes->json(),
                'spk_list'      => $latestSpk->ok() ? Arr::get($latestSpk->json(), 'data', []) : [],
                'transaksi_list'=> $latestTrx->ok() ? Arr::get($latestTrx->json(), 'data', []) : [],
            ], 200);

        } catch (Exception $e) {
            Log::error('Pengerjaan error: '.$e->getMessage());

            return response()->json([
                'message' => 'Terjadi kesalahan pada server',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper: pastikan nilai menjadi array (kalau dikirim sebagai JSON string).
     */
    private function asArray($value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }
        return is_array($value) ? $value : [];
    }
}
