@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4 text-xl font-bold">Manajemen Proses</h4>

    <!-- Form Input Nama Customer -->
    <div id="customerForm" class="mb-4">
        <label for="customerName" class="form-label">Masukkan Nama Customer:</label>
        <div class="input-group">
            <input type="text" id="customerName" class="form-control" placeholder="Masukkan nama customer untuk melihat proses">
            <button class="btn btn-primary" onclick="cekCustomer()">Lihat Proses</button>
        </div>
    </div>

    {{-- Kontrol Tabel Atas --}}
    <div id="tableControls" class="d-flex justify-content-between mb-2 align-items-center flex-wrap d-none">
        <div class="mb-2">
            <label>
                Show
                <select id="entriesPerPage" class="form-select d-inline-block w-auto">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                </select>
                entries
            </label>
        </div>

        <div class="mb-2">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="searchInput" class="form-control" placeholder="Cari proses (status, keterangan, mekanik...)">
            </div>
        </div>
    </div>

    <div class="table-responsive d-none" id="prosesSection">
        <table class="table table-bordered table-striped table-hover" id="prosesTable">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>ID Proses</th>
                    <th>ID Transaksi</th>
                    <th>ID Mekanik</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                    <th>Waktu Mulai</th>
                    <th>Waktu Selesai</th>
                </tr>
            </thead>
            <tbody id="prosesTableBody"></tbody>
        </table>
    </div>

    {{-- Kontrol Tabel Bawah --}}
    <div id="paginationSection" class="d-flex justify-content-between align-items-center flex-wrap d-none">
        <div id="tableInfo" class="mb-2"></div>
        <div id="pageNumbers" class="mb-2"></div>
    </div>
</div>

<script>
const API_URL = '{{ env('API_URL', 'https://apibengkel.up.railway.app/api') }}';
const token = "{{ session('token') }}";

let currentPage = 1;
let totalEntries = 0;
let totalPages = 0;
let perPage = 10;
let allData = [];
let searchQuery = "";
let selectedCustomer = null;

// === CEK CUSTOMER ===
async function cekCustomer() {
    const nama = document.getElementById('customerName').value.trim().toLowerCase();
    if (!nama) {
        alert("Nama harus diisi!");
        return;
    }

    try {
        const res = await fetch(`${API_URL}/customers`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        if (!res.ok) throw new Error("Gagal memuat data customer");

        const result = await res.json();
        const customers = Array.isArray(result.data) ? result.data : [];

        const found = customers.find(c => c.nama_customer.toLowerCase() === nama);
        if (!found) {
            alert("Nama tidak ditemukan di database.");
            return;
        }

        selectedCustomer = found.nama_customer;

        document.getElementById('customerForm').classList.add('d-none');
        document.getElementById('tableControls').classList.remove('d-none');
        document.getElementById('prosesSection').classList.remove('d-none');
        document.getElementById('paginationSection').classList.remove('d-none');

        loadProses();
    } catch (err) {
        console.error(err);
        alert("Terjadi kesalahan saat memeriksa nama customer.");
    }
}

// === LOAD DATA PROSES ===
async function loadProses() {
    if (!selectedCustomer) return;

    try {
        const res = await fetch(`${API_URL}/proses`);
        const result = await res.json();
        allData = result.data || result || [];

        // Filter hanya data dengan customer sesuai input
        allData = allData.filter(p => 
            (p.customer || '').toLowerCase() === selectedCustomer.toLowerCase()
        );

        allData.sort((a, b) => b.id_proses - a.id_proses); 
        renderTable();
    } catch (e) {
        console.error("Gagal load data proses:", e);
    }
}

// === RENDER TABEL ===
function renderTable() {
    let data = [...allData];

    // Filter pencarian
    if (searchQuery) {
        data = data.filter(p =>
            String(p.id_proses || '').includes(searchQuery) ||
            String(p.id_transaksi || '').includes(searchQuery) ||
            String(p.id_mekanik || '').includes(searchQuery) ||
            (p.customer || '').toLowerCase().includes(searchQuery) ||
            (p.status || '').toLowerCase().includes(searchQuery) ||
            (p.keterangan || '').toLowerCase().includes(searchQuery)
        );
    }

    totalEntries = data.length;
    totalPages = Math.ceil(totalEntries / perPage);

    const tbody = document.getElementById('prosesTableBody');
    tbody.innerHTML = '';

    if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="9" class="text-center">Tidak ada data</td></tr>`;
        document.getElementById('tableInfo').textContent = `Menampilkan 0 dari 0 data`;
        return;
    }

    const startIdx = (currentPage - 1) * perPage;
    const pageItems = data.slice(startIdx, startIdx + perPage);

    pageItems.forEach((p, i) => {
        let badge;
        const status = (p.status || '').toLowerCase();
        if (!p.status) {
            badge = '<span class="badge bg-light text-dark"><i class="bi bi-hourglass-split"></i> Belum Mulai</span>';
        } else if (status.includes('batal')) {
            badge = '<span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Dibatalkan</span>';
        } else if (p.waktu_selesai) {
            badge = '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Selesai</span>';
        } else {
            badge = '<span class="badge bg-warning text-dark"><i class="bi bi-gear"></i> Diproses</span>';
        }

        tbody.innerHTML += `
            <tr>
                <td>${startIdx + i + 1}</td>
                <td>${p.id_proses || '-'}</td>
                <td>${p.id_transaksi || '-'}</td>
                <td>${p.id_mekanik || '-'}</td>
                <td>${p.customer || '-'}</td>
                <td>${badge}</td>
                <td>${p.keterangan || '-'}</td>
                <td>${p.waktu_mulai ? new Date(p.waktu_mulai).toLocaleString('id-ID') : '-'}</td>
                <td>${p.waktu_selesai ? new Date(p.waktu_selesai).toLocaleString('id-ID') : '-'}</td>
            </tr>`;
    });

    const endEntry = Math.min(startIdx + pageItems.length, totalEntries);
    document.getElementById('tableInfo').textContent =
        `Menampilkan ${startIdx + 1} sampai ${endEntry} dari ${totalEntries} data`;

    renderPagination();
}

// === RENDER PAGINATION ===
function renderPagination() {
    const container = document.getElementById('pageNumbers');
    container.innerHTML = '';
    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        btn.className = `btn btn-sm mx-1 ${i === currentPage ? 'btn-primary' : 'btn-outline-primary'}`;
        btn.onclick = () => {
            currentPage = i;
            renderTable();
        };
        container.appendChild(btn);
    }
}

// === EVENT LISTENER ===
document.getElementById('entriesPerPage').addEventListener('change', function () {
    perPage = parseInt(this.value);
    currentPage = 1;
    renderTable();
});

document.getElementById('searchInput').addEventListener('input', function () {
    searchQuery = this.value.toLowerCase();
    currentPage = 1;
    renderTable();
});

// Auto-refresh setiap 5 detik
setInterval(loadProses, 5000);
</script>
@endsection
