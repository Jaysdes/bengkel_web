@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-0 text-dark fw-bold">
                <i class="fas fa-list"></i> Daftar Transaksi
            </h4>
            <p class="text-muted">Kelola semua transaksi bengkel</p>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-3">
        <div class="col-md-6">
            <a href="{{ route('transaksi') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Transaksi
            </a>
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-outline-primary" onclick="exportData()">
                <i class="fas fa-download"></i> Export
            </button>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3"> 
                <div class="col-md-4">
                    <label class="form-label">Show</label>
                    <select id="entriesPerPage" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Pencarian</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari customer, kendaraan, dll...">
                    </div>
                </div>
            
                <div class="col-md-3">
                    <label class="form-label">Filter Status</label>
                    <select id="statusFilter" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="lunas">Lunas</option>
                        <option value="belum_lunas">Belum Lunas</option>
                    </select>
                </div>   
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0"><i class="fas fa-table"></i> Tabel Transaksi</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="tabelTransaksi" class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#ID</th>
                            <th>Customer</th>
                            <th>Kendaraan</th>
                            <th>No Kendaraan</th>
                            <th>Mekanik</th>
                            <th>Harga Jasa</th>
                            <th>Harga Sparepart</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bodyTransaksi">
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p id="tableInfo" class="mb-0 text-muted">Menampilkan data...</p>
                </div>
                <div class="col-md-6">
                    <nav aria-label="Table pagination">
                        <ul class="pagination justify-content-end mb-0">
                            <li class="page-item">
                                <button class="page-link" onclick="prevPage()">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                            </li>
                            <div id="pageNumbers"></div>
                            <li class="page-item">
                                <button class="page-link" onclick="nextPage()">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
<div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>


<script>
const token = "{{ session('token') }}";
const API_URL = 'https://apibengkel.up.railway.app/api';
const customerMap = {};
const jenisMap = {};
const mekanikMap = {};

let currentPage = 1;
let totalEntries = 0;
let totalPages = 0;

document.addEventListener("DOMContentLoaded", function () {
    loadReferenceData().then(() => {
        tampilkanTabelTransaksi();
    });

    document.getElementById('searchInput').addEventListener('input', debounce(filterData, 300));
    document.getElementById('entriesPerPage').addEventListener('change', filterData);
    document.getElementById('statusFilter').addEventListener('change', filterData);
});

// === LOAD DATA TRANSAKSI ===
function tampilkanTabelTransaksi() {
    const limit = parseInt(document.getElementById('entriesPerPage').value);
    const search = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;

    fetch(`${API_URL}/transaksi`, {
        headers: { Authorization: `Bearer ${token}` }
    })
        .then(res => res.json())
        .then(result => {
            let data = result.data || [];

            // Filtering
            data = data.filter(row => {
                const customerName = customerMap[row.id_customer] ?? '';
                const jenisName = jenisMap[row.id_jenis] ?? '';
                const mekanikName = mekanikMap[row.id_mekanik] ?? '';
                
                const matchesSearch = 
                    row.no_kendaraan?.toLowerCase().includes(search) ||
                    customerName.toLowerCase().includes(search) ||
                    jenisName.toLowerCase().includes(search) ||
                    mekanikName.toLowerCase().includes(search) ||
                    String(row.total).includes(search);

                const status = row.status_pembayaran || 'belum_lunas';
                const matchesStatus = !statusFilter || status === statusFilter;

                return matchesSearch && matchesStatus;
            });

            totalEntries = data.length;
            totalPages = Math.ceil(totalEntries / limit);
            const startIndex = (currentPage - 1) * limit;
            const dataPaginated = data.slice(startIndex, startIndex + limit);

            renderTable(dataPaginated, startIndex);
            renderPagination();
            updateTableInfo(startIndex, dataPaginated.length);
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Gagal memuat data transaksi', 'danger');
        });
}

// === RENDER TABLE ===
function renderTable(data, startIndex) {
    const tbody = document.getElementById('bodyTransaksi');
    tbody.innerHTML = '';

    if (data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center py-4 text-muted">
                    <i class="fas fa-search"></i> Tidak ada data ditemukan
                </td>
            </tr>
        `;
        return;
    }

    data.forEach((row) => {
        const status = row.status_pembayaran || 'belum_lunas';
        const customerName = customerMap[row.id_customer] ?? `Customer #${row.id_customer}`;
        const jenisName = jenisMap[row.id_jenis] ?? `Jenis #${row.id_jenis}`;
        const mekanikName = mekanikMap[row.id_mekanik] ?? `Mekanik #${row.id_mekanik}`;

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="fw-bold text-primary">#${String(row.id_transaksi).padStart(4, '0')}</td>
            <td>${customerName}</td>
            <td>${jenisName}</td>
            <td><span class="badge bg-secondary">${row.no_kendaraan}</span></td>
            <td>${mekanikName}</td>
            <td class="text-end">Rp ${formatCurrency(row.harga_jasa)}</td>
            <td class="text-end">Rp ${formatCurrency(row.harga_sparepart)}</td>
            <td class="text-end"><strong class="text-success">Rp ${formatCurrency(row.total)}</strong></td>
            <td>
                <span class="badge ${status === 'lunas' ? 'bg-success' : 'bg-warning'}">
                    ${status === 'lunas' ? 'Lunas' : 'Belum Lunas'}
                </span>
            </td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu">
                        ${status !== 'lunas' ? `
                        <li>
                            <a class="dropdown-item" href="{{ route('validasi') }}">
                                <i class="fas fa-money-bill-wave"></i> Validasi
                            </a>
                        </li>` : ''}
                        <li>
                            <a class="dropdown-item" onclick="cetakNota(${row.id_transaksi})">
                                <i class="fas fa-receipt"></i> Cetak Nota 
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" onclick="editTransaksi(${row.id_transaksi})">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" onclick="deleteTransaksi(${row.id_transaksi})">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// === CETAK NOTA ===
function cetakNota(id) {
    window.location.href = `/nota/${id}`;
}

// === REFERENSI DATA ===
function loadReferenceData() {
    return Promise.all([
        fetch(`${API_URL}/customers`, { headers: { Authorization: `Bearer ${token}` }}).then(res => res.json()),
        fetch(`${API_URL}/jenis_kendaraan`, { headers: { Authorization: `Bearer ${token}` }}).then(res => res.json()),
        fetch(`${API_URL}/mekanik`, { headers: { Authorization: `Bearer ${token}` }}).then(res => res.json()),
    ])
    .then(([customerRes, jenisRes, mekanikRes]) => {
        (customerRes.data || []).forEach(c => customerMap[c.id_customer] = c.nama_customer);
        (jenisRes.data || []).forEach(j => jenisMap[j.id_jenis] = j.jenis_kendaraan);
        (mekanikRes.data || []).forEach(m => mekanikMap[m.id_mekanik] = m.nama_mekanik);
    });
}

// === EDIT & DELETE ===
function editTransaksi(id) {
    window.location.href = `/transaksi?edit=${id}`;
}
function deleteTransaksi(id) {
    if (confirm('Yakin ingin menghapus transaksi ini?')) {
        fetch(`${API_URL}/transaksi/${id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })
        .then(() => {
            showToast('Transaksi berhasil dihapus', 'success');
            tampilkanTabelTransaksi();
        })
        .catch(() => showToast('Gagal menghapus transaksi', 'danger'));
    }
}

// === UTILS ===
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID').format(amount || 0);
}
function debounce(func, delay) {
    let timeoutId;
    return function (...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    document.getElementById('toast-container').appendChild(toast);
    new bootstrap.Toast(toast).show();
    toast.addEventListener('hidden.bs.toast', () => toast.remove());
}
</script>
@endsection
