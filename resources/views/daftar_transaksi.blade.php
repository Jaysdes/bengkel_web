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
            <button class="btn btn-success" onclick="refreshData()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
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
                    <label class="form-label">Pencarian</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari customer, kendaraan, dll...">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Show</label>
                    <select id="entriesPerPage" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Filter Status</label>
                    <select id="statusFilter" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="lunas">Lunas</option>
                        <option value="belum_lunas">Belum Lunas</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Filter Tanggal</label>
                    <input type="date" id="dateFilter" class="form-control">
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
                            <th width="8%">#ID</th>
                            <th width="15%">Customer</th>
                            <th width="10%">Kendaraan</th>
                            <th width="12%">No Kendaraan</th>
                            <th width="12%">Mekanik</th>
                            <th width="10%">Harga Jasa</th>
                            <th width="12%">Harga Sparepart</th>
                            <th width="10%">Total</th>
                            <th width="8%">Status</th>
                            <th width="8%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bodyTransaksi">
                        <!-- Data will be loaded dynamically -->
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

<style>
.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.1);
    transform: scale(1.01);
    transition: all 0.2s ease;
}

.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}

.badge {
    font-size: 0.75em;
}

.table th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
}

.pagination .page-link {
    border-radius: 50% !important;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 2px;
}
</style>

<script>
const API_URL = '{{ env('API_URL', 'http://localhost:8001/api') }}';
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

    // Event listeners
    document.getElementById('searchInput').addEventListener('input', debounce(filterData, 300));
    document.getElementById('entriesPerPage').addEventListener('change', filterData);
    document.getElementById('statusFilter').addEventListener('change', filterData);
    document.getElementById('dateFilter').addEventListener('change', filterData);
});

function tampilkanTabelTransaksi() {
    const limit = parseInt(document.getElementById('entriesPerPage').value);
    const search = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;

    fetch(`${API_URL}/transaksi`)
        .then(res => res.json())
        .then(result => {
            let data = result.data || [];

            // Apply filters
            data = data.filter(row => {
                const customerName = customerMap[row.id_customer] ?? '';
                const jenisName = jenisMap[row.id_jenis] ?? '';
                const mekanikName = mekanikMap[row.id_mekanik] ?? '';
                
                // Text search
                const matchesSearch = 
                    row.no_kendaraan?.toLowerCase().includes(search) ||
                    customerName.toLowerCase().includes(search) ||
                    jenisName.toLowerCase().includes(search) ||
                    mekanikName.toLowerCase().includes(search) ||
                    String(row.total).includes(search);

                // Status filter
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
            document.getElementById('bodyTransaksi').innerHTML = `
                <tr>
                    <td colspan="10" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p class="mt-2 mb-0">Gagal memuat data transaksi</p>
                    </td>
                </tr>
            `;
        });
}

function renderTable(data, startIndex) {
    const tbody = document.getElementById('bodyTransaksi');
    tbody.innerHTML = '';

    if (data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center py-4">
                    <i class="fas fa-search"></i>
                    <p class="mt-2 mb-0">Tidak ada data yang ditemukan</p>
                </td>
            </tr>
        `;
        return;
    }

    data.forEach((row, index) => {
        const status = row.status_pembayaran || 'belum_lunas';
        const customerName = customerMap[row.id_customer] ?? `Customer #${row.id_customer}`;
        const jenisName = jenisMap[row.id_jenis] ?? `Jenis #${row.id_jenis}`;
        const mekanikName = mekanikMap[row.id_mekanik] ?? `Mekanik #${row.id_mekanik}`;

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="fw-bold text-primary">#${String(row.id_transaksi).padStart(4, '0')}</td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div>
                        <div class="fw-medium">${customerName}</div>
                        <small class="text-muted">${jenisName}</small>
                    </div>
                </div>
            </td>
            <td>${jenisName}</td>
            <td>
                <span class="badge bg-secondary">${row.no_kendaraan}</span>
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <i class="fas fa-user-cog text-info me-2"></i>
                    ${mekanikName}
                </div>
            </td>
            <td class="text-end">
                <strong>Rp ${formatCurrency(row.harga_jasa)}</strong>
            </td>
            <td class="text-end">
                <strong>Rp ${formatCurrency(row.harga_sparepart)}</strong>
            </td>
            <td class="text-end">
                <h6 class="mb-0 text-success">Rp ${formatCurrency(row.total)}</h6>
            </td>
            <td>
                <span class="badge ${status === 'lunas' ? 'bg-success' : 'bg-warning'}">
                    <i class="fas ${status === 'lunas' ? 'fa-check-circle' : 'fa-clock'}"></i>
                    ${status === 'lunas' ? 'Lunas' : 'Pending'}
                </span>
            </td>
            <td>
                <div class="dropdown ">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu bg-dark">
                        <li>
                            <a class="dropdown-item" href="/nota/${row.id_transaksi}">
                                <i class="fas fa-receipt"></i> Print Invoice
                            </a>
                        </li>
                        ${status !== 'lunas' ? `
                        <li>
                            <a class="dropdown-item" href="#" onclick="markAsPaid(${row.id_transaksi})">
                                <i class="fas fa-money-bill-wave"></i> Mark as Paid
                            </a>
                        </li>
                        ` : ''}
                        <li>
                            <a class="dropdown-item" href="#" onclick="editTransaksi(${row.id_transaksi})">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="#" onclick="deleteTransaksi(${row.id_transaksi})">
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

// Remove the showLoading function as it's no longer needed

function loadReferenceData() {
    return Promise.all([
        fetch(`${API_URL}/customers`).then(res => res.json()),
        fetch(`${API_URL}/jenis_kendaraan`).then(res => res.json()), 
        fetch(`${API_URL}/mekanik`).then(res => res.json()),
    ])
    .then(([customerRes, jenisRes, mekanikRes]) => {
        (customerRes.data || []).forEach(c => customerMap[c.id_customer] = c.nama_customer);
        (jenisRes.data || []).forEach(j => jenisMap[j.id_jenis] = j.jenis_kendaraan);
        (mekanikRes.data || []).forEach(m => mekanikMap[m.id_mekanik] = m.nama_mekanik);
    })
    .catch(error => {
        console.error('Error loading reference data:', error);
        showToast('Gagal memuat data referensi', 'warning');
    });
}

function renderPagination() {
    const pageContainer = document.getElementById('pageNumbers');
    pageContainer.innerHTML = '';

    const maxVisiblePages = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

    if (endPage - startPage < maxVisiblePages - 1) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }

    for (let i = startPage; i <= endPage; i++) {
        const li = document.createElement('li');
        li.className = `page-item ${i === currentPage ? 'active' : ''}`;
        
        const button = document.createElement('button');
        button.className = 'page-link';
        button.textContent = i;
        button.onclick = () => {
            currentPage = i;
            tampilkanTabelTransaksi();
        };
        
        li.appendChild(button);
        pageContainer.appendChild(li);
    }
}

function updateTableInfo(startIndex, dataLength) {
    const endEntry = startIndex + dataLength;
    document.getElementById('tableInfo').textContent = 
        `Menampilkan ${startIndex + 1} sampai ${endEntry} dari ${totalEntries} entri`;
}

function filterData() {
    currentPage = 1;
    tampilkanTabelTransaksi();
}

function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        tampilkanTabelTransaksi();
    }
}

function nextPage() {
    if (currentPage < totalPages) {
        currentPage++;
        tampilkanTabelTransaksi();
    }
}

function refreshData() {
    showToast('Memuat ulang data...', 'info');
    tampilkanTabelTransaksi();
}

function editTransaksi(id) {
    window.location.href = `/transaksi?edit=${id}`;
}

function deleteTransaksi(id) {
    if (confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) {
        fetch(`${API_URL}/transaksi/${id}`, {
            method: 'DELETE'
        })
        .then(res => res.json())
        .then(result => {
            showToast('Transaksi berhasil dihapus', 'success');
            tampilkanTabelTransaksi();
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Gagal menghapus transaksi', 'danger');
        });
    }
}

function exportData() {
    showToast('Fitur export akan segera tersedia', 'info');
}

function markAsPaid(transaksiId) {
    if (confirm('Apakah Anda yakin ingin menandai transaksi ini sebagai lunas?')) {
        fetch(`${API_URL}/transaksi/${transaksiId}/mark-paid`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            showToast('Transaksi berhasil ditandai sebagai lunas!', 'success');
            // Refresh the table data
            setTimeout(() => {
                tampilkanTabelTransaksi();
            }, 1500);
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Gagal menandai transaksi sebagai lunas. Silakan coba lagi.', 'danger');
        });
    }
}

// Utility functions
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
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-info-circle me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    document.getElementById('toast-container').appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}
</script>
@endsection