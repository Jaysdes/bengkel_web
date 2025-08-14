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
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalBayarLabel">Form Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="bayar-id-transaksi">
                    <div class="mb-3">
                        <label>Total</label>
                        <input type="text" id="bayar-total" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label>Jumlah Bayar</label>
                        <input type="number" id="bayar-jumlah" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Bayar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    loadData();

    function loadData() {
        fetch('http://localhost:8001/api/detail_transaksi')
            .then(res => res.json())
            .then(res => {
                let html = '';
                if (res.data && res.data.length > 0) {
                    res.data.forEach((item, index) => {
                        html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.id_transaksi}</td>
                                <td>${item.no_spk ?? '-'}</td>
                                <td>${item.customer?.nama ?? '-'}</td>
                                <td>${item.no_kendaraan ?? '-'}</td>
                                <td class="text-end">${formatRupiah(item.total)}</td>
                                <td class="text-end">${formatRupiah(item.bayar)}</td>
                                <td class="text-end">${formatRupiah(item.kembalian)}</td>
                                <td>
                                    ${item.status_pembayaran === 'lunas' 
                                        ? '<span class="badge bg-success">Lunas</span>'
                                        : '<span class="badge bg-danger">Belum Lunas</span>'
                                    }
                                </td>
                                <td>
                                    ${item.status_pembayaran !== 'lunas' 
                                        ? `<button class="btn btn-sm btn-success" onclick="showFormBayar(${item.id_transaksi}, ${item.total})">Bayar</button>` 
                                        : ''
                                    }
                                    <a href="http://localhost:8001/api/transaksi/${item.id_transaksi}/nota" target="_blank" class="btn btn-sm btn-primary">Cetak Nota</a>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    html = `<tr><td colspan="10" class="text-center">Data kosong</td></tr>`;
                }
                document.getElementById("data-validasi").innerHTML = html;
            })
            .catch(err => {
                console.error(err);
                document.getElementById("data-validasi").innerHTML = `<tr><td colspan="10" class="text-center">Gagal memuat data</td></tr>`;
            });
    }

    window.showFormBayar = function(id, total) {
        document.getElementById("bayar-id-transaksi").value = id;
        document.getElementById("bayar-total").value = formatRupiah(total);
        document.getElementById("bayar-jumlah").value = '';
        new bootstrap.Modal(document.getElementById('modalBayar')).show();
    }

    document.getElementById("form-bayar").addEventListener("submit", function(e) {
        e.preventDefault();
        let id = document.getElementById("bayar-id-transaksi").value;
        let jumlahBayar = document.getElementById("bayar-jumlah").value;

        fetch(`http://localhost:8001/api/transaksi/${id}/bayar`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ bayar: jumlahBayar })
        })
        .then(res => res.json())
        .then(res => {
            alert(res.message);
            bootstrap.Modal.getInstance(document.getElementById('modalBayar')).hide();
            loadData();
        })
        .catch(err => {
            console.error(err);
            alert("Gagal memproses pembayaran");
        });
    });

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(angka);
    }
});
</script>
@endsection
