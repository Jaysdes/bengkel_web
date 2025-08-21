@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4 text-xl font-bold">Manajemen Proses</h4>
    <a href="{{ route('validasi') }}" class="btn btn-primary mb-3">
        <i class="bi bi-cash-coin"></i> Validasi Pembayaran
    </a>

    {{-- Kontrol Tabel Atas --}}
    <div class="d-flex justify-content-between mb-2 align-items-center flex-wrap">
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

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover" id="prosesTable">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>ID Proses</th>
                    <th>ID Transaksi</th>
                    <th>ID Mekanik</th>
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
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div id="tableInfo" class="mb-2"></div>
        <div id="pageNumbers" class="mb-2"></div>
    </div>
</div>

<script>
const API_URL = '{{ env('API_URL', 'https://apibengkel.up.railway.app/api') }}';

let currentPage = 1;
let totalEntries = 0;
let totalPages = 0;
let perPage = 10;
let allData = [];
let searchQuery = "";

async function loadProses() {
    try {
        const res = await fetch(`${API_URL}/proses`);
        const result = await res.json();
        allData = result.data || result || [];
        allData.sort((a, b) => b.id_proses - a.id_proses); 
        renderTable();
    } catch (e) {
        console.error("Gagal load data proses:", e);
    }
}

function renderTable() {
    let data = [...allData];

    // Filter pencarian (lebih lengkap)
    if (searchQuery) {
        data = data.filter(p =>
            String(p.id_proses || '').includes(searchQuery) ||
            String(p.id_transaksi || '').includes(searchQuery) ||
            String(p.id_mekanik || '').includes(searchQuery) ||
            (p.status || '').toLowerCase().includes(searchQuery) ||
            (p.keterangan || '').toLowerCase().includes(searchQuery)
        );
    }

    totalEntries = data.length;
    totalPages = Math.ceil(totalEntries / perPage);

    const tbody = document.getElementById('prosesTableBody');
    tbody.innerHTML = '';

    if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>`;
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

loadProses();
</script>
@endsection
