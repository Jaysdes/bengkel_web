@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4 font-bold text-xl">Laporan & Pembayaran</h4>

    <!-- Search -->
    <div class="row mb-3">
        <div class="col-md-4">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari transaksi...">
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-sm">
            <thead class="table-dark">
                <tr>
                    <th>No Transaksi</th>
                    <th>Customer</th>
                    <th>No Kendaraan</th>
                    <th>Total</th>
                    <th>Status Pembayaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="laporanTableBody"></tbody>
        </table>
    </div>

    <!-- Table Info & Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div id="tableInfo" class="text-muted"></div>
        <div id="pageNumbers"></div>
    </div>
</div>

<script>
    const token = "{{ session('token') }}";
    const apiBase = 'http://localhost:8001/api';
    let allData = [];
    let filteredData = [];
    let currentPage = 1;
    const rowsPerPage = 5;
    const customerMap = {};
    document.addEventListener('DOMContentLoaded', () => {
        loadLaporan();
        document.getElementById('searchInput').addEventListener('input', searchData);
    });

    async function loadLaporan() {
        try {
            const res = await fetch(`${apiBase}/transaksi`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            if (!res.ok) throw new Error(`Gagal memuat data (${res.status})`);
            const result = await res.json();
            allData = Array.isArray(result.data) ? result.data : [];
            filteredData = allData;
            currentPage = 1;
            renderTable();
            renderPagination();
            updateTableInfo();
        } catch (err) {
            console.error(err);
            alert('Gagal memuat laporan transaksi.');
        }
    }

    function renderTable() {
        const tbody = document.getElementById('laporanTableBody');
        tbody.innerHTML = '';

        const start = (currentPage - 1) * rowsPerPage;
        const paginatedItems = filteredData.slice(start, start + rowsPerPage);

        if (paginatedItems.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">Tidak ada data</td></tr>`;
            return;
        }

        paginatedItems.forEach(item => {
            const status = item.status_pembayaran?.toLowerCase() === 'lunas'
                ? '<span class="badge bg-success">Lunas</span>'
                : '<span class="badge bg-danger">Belum Lunas</span>';
            const customerName = customerMap[item.id_customer] ?? item.id_customer;
            const row = `
                <tr>
                    <td>${item.id_transaksi || '-'}</td>
                    <td>${customerName}</td>
                    <td>${item.no_kendaraan || '-'}</td>
                    <td>${formatRupiah(item.total || 0)}</td>
                    <td>${status}</td>
                    <td>
                        ${item.status_pembayaran?.toLowerCase() !== 'lunas' 
                            ? `<button class="btn btn-sm btn-primary me-1" onclick="validasiPembayaran(${item.id_transaksi})">Validasi</button>` 
                            : ''
                        }
                        <button class="btn btn-sm btn-secondary" onclick="cetakNota(${item.id_transaksi})">Cetak Nota</button>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    }

    function renderPagination() {
        const totalPages = Math.ceil(filteredData.length / rowsPerPage);
        const pageContainer = document.getElementById('pageNumbers');
        pageContainer.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.className = `btn btn-sm mx-1 ${i === currentPage ? 'btn-primary' : 'btn-outline-primary'}`;
            btn.onclick = () => {
                currentPage = i;
                renderTable();
                updateTableInfo();
            };
            pageContainer.appendChild(btn);
        }
    }

    function updateTableInfo() {
        const total = filteredData.length;
        const start = total ? (currentPage - 1) * rowsPerPage + 1 : 0;
        let end = total ? start + rowsPerPage - 1 : 0;
        if (end > total) end = total;

        document.getElementById('tableInfo').innerText =
            total === 0
                ? "Menampilkan 0 dari 0 data"
                : `Menampilkan ${start} sampai ${end} dari ${total} data`;
    }

    function searchData() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        filteredData = allData.filter(item =>
            (item.id_transaksi || '').toString().includes(query) ||
            (item.customer?.nama || '').toLowerCase().includes(query) ||
            (item.no_kendaraan || '').toLowerCase().includes(query)
        );
        currentPage = 1;
        renderTable();
        renderPagination();
        updateTableInfo();
    }

    async function validasiPembayaran(id) {
        if (!confirm("Apakah Anda yakin ingin memvalidasi pembayaran ini?")) return;

        try {
            const res = await fetch(`${apiBase}/transaksi/${id}/bayar`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` }
            });
            const result = await res.json();
            alert(result.message || "Pembayaran berhasil divalidasi");
            loadLaporan();
        } catch (err) {
            console.error(err);
            alert("Gagal memvalidasi pembayaran.");
        }
    }

    function cetakNota(id) {
        window.open(`/nota/${id}`, '_blank');
    }

    function formatRupiah(angka) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
    }
</script>
@endsection
