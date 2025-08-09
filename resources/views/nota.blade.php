@extends('layouts.app')

@section('content')
<div class="container mt-4" id="nota">
    <h3 class="text-center">Bengkel Motor Jaya</h3>
    <p class="text-center">Jl. Contoh No. 123, Jakarta</p>
    <hr>

    <div class="mb-3">
        <strong>No Transaksi:</strong> {{ $transaksi['id_transaksi'] }} <br>
        <strong>Tanggal:</strong> {{ date('d-m-Y', strtotime($transaksi['created_at'] ?? now())) }}<br>

        <strong>Customer:</strong> {{ $transaksi['customer']['nama'] ?? '-' }} <br>
        <strong>No Kendaraan:</strong> {{ $transaksi['no_kendaraan'] }}
    </div>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Deskripsi</th>
                <th class="text-end">Harga (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Jasa</td>
                <td class="text-end">{{ number_format($transaksi['harga_jasa'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Sparepart</td>
                <td class="text-end">{{ number_format($transaksi['harga_sparepart'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Total</th>
                <th class="text-end">{{ number_format($transaksi['total'], 0, ',', '.') }}</th>
            </tr>
        </tbody>
    </table>

    <p class="mt-4">Status Pembayaran:
        @if($transaksi['status_pembayaran'] === 'lunas')
            <span class="badge bg-success">Lunas</span>
        @else
            <span class="badge bg-danger">Belum Lunas</span>
        @endif
    </p>

    <div class="text-center mt-5">
        <button class="btn btn-primary" onclick="window.print()">Cetak</button>
    </div>
</div>
@endsection
