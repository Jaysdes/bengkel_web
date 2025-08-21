@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3>Validasi Pembayaran</h3>
    <hr>

    <table class="table table-bordered" id="tabel-validasi">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>No Transaksi</th>
                <th>No SPK</th>
                <th>Customer</th>
                <th>No Kendaraan</th>
                <th>Total (Rp)</th>
                <th>Bayar (Rp)</th>
                <th>Kembalian (Rp)</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="data-validasi">
            <tr>
                <td colspan="10" class="text-center">Memuat data...</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Modal Pembayaran -->
<div class="modal fade" id="modalBayar" tabindex="-1" aria-labelledby="modalBayarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="form-bayar">
            <div class="modal-content bg-dark">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="modalBayarLabel">Form Pembayaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-white">
                    <input type="hidden" id="bayar-id-transaksi">
                    <input type="hidden" id="bayar-total-num">
                    <div class="mb-3">
                        <label>Total</label>
                        <input type="text" id="bayar-total" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label>Jumlah Bayar</label>
                        <input type="number" id="bayar-jumlah" class="form-control" min="0" required>
                    </div>
                </div>
                <div class="modal-footer text-white">
                    <button type="submit" class="btn btn-success">Bayar</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
const API_URL = '{{ env('API_URL', 'https://apibengkel.up.railway.app/api') }}';
const token = "{{ session('token') }}";

document.addEventListener("DOMContentLoaded", function() {
    loadData();

    async function loadData() {
        const res = await fetch(`${API_URL}/detail_transaksi`, {
            headers: token ? { Authorization: `Bearer ${token}` } : {}
        });
        const data = await res.json();
        const rows = data.data || data || [];
        const tbody = document.getElementById("data-validasi");

        if (!Array.isArray(rows) || rows.length === 0) {
            tbody.innerHTML = `<tr><td colspan="10" class="text-center">Data kosong</td></tr>`;
            return;
        }

        let html = '';
rows.forEach((item, index) => {
    const total = Number(item.total || 0);
    const bayar = Number(item.bayar || 0);
    const kembalian = Number(item.kembalian || 0);

    const statusLunas = (item.status_pembayaran && item.status_pembayaran.toLowerCase() === 'lunas') || (bayar >= total);
    const statusBatal = item.status_pembayaran && item.status_pembayaran.toLowerCase() === 'dibatalkan';

    const statusHtml = statusBatal
        ? '<span class="badge bg-secondary">Dibatalkan</span>'
        : statusLunas ? '<span class="badge bg-success">Lunas</span>'
        : '<span class="badge bg-danger">Belum Lunas</span>';

    html += `
        <tr>
            <td>${index + 1}</td>
            <td>${item.id_transaksi}</td>
            <td>${item.no_spk ?? '-'}</td>
            <td>${item.id_customer ?? '-'}</td>   <!-- pakai ID customer dulu -->
            <td>${item.no_kendaraan ?? '-'}</td>
            <td class="text-end">${formatRupiah(total)}</td>
            <td class="text-end">${formatRupiah(bayar)}</td>
            <td class="text-end">${formatRupiah(kembalian)}</td>
            <td>${statusHtml}</td>
            <td class="d-flex gap-1">
                ${(!statusLunas && !statusBatal)
                    ? `<button class="btn btn-sm btn-success" onclick="showFormBayar(${item.id_detail}, ${total})">Bayar</button>
                       <button class="btn btn-sm btn-outline-secondary" onclick="batalkanTransaksi(${item.id_detail})">Batalkan</button>`
                    : ''
                }
                <a href="/nota/${item.id_transaksi}" target="_blank" class="btn btn-sm btn-primary">Cetak Nota</a>
            </td>
        </tr>`;
});
tbody.innerHTML = html;

    }

    window.showFormBayar = function(id, total) {
        document.getElementById("bayar-id-transaksi").value = id;
        document.getElementById("bayar-total").value = formatRupiah(total);
        document.getElementById("bayar-total-num").value = total;
        document.getElementById("bayar-jumlah").value = total;
        new bootstrap.Modal(document.getElementById('modalBayar')).show();
    }

    document.getElementById("form-bayar").addEventListener("submit", async function(e) {
        e.preventDefault();
        const id = document.getElementById("bayar-id-transaksi").value;
        const totalNum = parseInt(document.getElementById("bayar-total-num").value || '0');
        const jumlahBayar = parseInt(document.getElementById("bayar-jumlah").value || '0');

        if (!jumlahBayar || jumlahBayar < totalNum) {
            alert(`Jumlah bayar harus ≥ total (${formatRupiah(totalNum)})`);
            return;
        }

        try {
            const res = await fetch(`${API_URL}/transaksi/${id}/bayar`, {
    method: 'PUT',
    headers: {
        'Content-Type': 'application/json',
        ...(token ? { Authorization: `Bearer ${token}` } : {})
    },
    body: JSON.stringify({ bayar: jumlahBayar })
});

            const js = await res.json();
            if (!res.ok) throw new Error(js.message || `Gagal (HTTP ${res.status})`);

            alert(`Pembayaran berhasil. Kembalian: ${formatRupiah(js.data?.kembalian ?? js.kembalian ?? (jumlahBayar - totalNum))}`);
            bootstrap.Modal.getInstance(document.getElementById('modalBayar')).hide();
            loadData();
        } catch (err) {
            console.error(err);
            alert("Gagal memproses pembayaran: " + err.message);
        }
    });

    window.batalkanTransaksi = async function(id) {
        if (!confirm('Batalkan transaksi ini?')) return;
        try {
            await fetch(`${API_URL}/transaksi/${id}/batal`, {
    method: 'PUT',
    headers: token ? { Authorization: `Bearer ${token}` } : {}
});

            alert('Transaksi dibatalkan');
            loadData();
        } catch (err) {
            console.error(err);
            alert('Gagal membatalkan transaksi');
        }
    }

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(Number(angka) || 0);
    }
});
</script>
@endsection