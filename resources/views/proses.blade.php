@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4">Manajemen Proses</h4>
    <button class="btn btn-primary mb-3">
        <a class="nav-link" href="{{ route('validasi') }}">
            <i class="bi bi-cash-coin"></i> Validasi Pembayaran
        </a>
 </button>

    <div class="mb-3">
        <input type="text" id="searchInput" class="form-control" placeholder="Cari berdasarkan status, keterangan, atau mekanik">
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="prosesTable">
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
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="prosesTableBody"></tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between">
        <div id="tableInfo"></div>
        <div id="pageNumbers" class="btn-group"></div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form id="formProses" onsubmit="submitForm(event)">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Proses</h5>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="id_proses">
                <div class="form-group">
                    <label>ID Transaksi</label>
                    <input type="number" class="form-control" id="id_transaksi" required>
                </div>
                <div class="form-group">
                    <label>ID Mekanik</label>
                    <input type="number" class="form-control" id="id_mekanik" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status">
                        <option value="belum diproses">Belum Diproses</option>
                        <option value="diproses">Diproses</option>
                        <option value="selesai">Selesai</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea class="form-control" id="keterangan" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Waktu Mulai</label>
                    <input type="datetime-local" class="form-control" id="waktu_mulai">
                </div>
                <div class="form-group">
                    <label>Waktu Selesai</label>
                    <input type="datetime-local" class="form-control" id="waktu_selesai" disabled>
                    <small class="text-muted">Diisi otomatis ketika pembayaran divalidasi</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </form>
  </div>
</div>

<script>
const API_URL = '{{ env('API_URL', 'http://localhost:8001/api') }}';

let currentPage = 1;
let totalEntries = 0;
let totalPages = 0;
let allData = [];

async function loadProses() {
    const perPage = 10;
    const res = await fetch(`${API_URL}/proses`);
    const result = await res.json();
    allData = result.data || result || [];
    totalEntries = allData.length;
    totalPages = Math.ceil(totalEntries / perPage);
    renderTable();
    renderPagination();
}

function renderTable() {
    const perPage = 10;
    const tbody = document.getElementById('prosesTableBody');
    const keyword = document.getElementById('searchInput').value.toLowerCase();

    const filtered = (allData || []).filter(p =>
        (p.status || '').toLowerCase().includes(keyword)
        || (p.keterangan || '').toLowerCase().includes(keyword)
        || String(p.id_mekanik || '').includes(keyword)
    );

    totalEntries = filtered.length;
    totalPages = Math.ceil(totalEntries / perPage);

    const start = (currentPage - 1) * perPage;
    const pageData = filtered.slice(start, start + perPage);

    tbody.innerHTML = '';
    pageData.forEach((p, i) => {
        const badge = !p.status ? '-' :
            (p.status || '').toLowerCase().includes('batal') ? '<span class="badge bg-secondary">Dibatalkan</span>' :
            p.waktu_selesai ? '<span class="badge bg-success">Selesai</span>' :
                              '<span class="badge bg-warning text-dark">Diproses</span>';

        tbody.innerHTML += `
            <tr>
                <td>${start + i + 1}</td>
                <td>${p.id_proses || '-'}</td>
                <td>${p.id_transaksi || '-'}</td>
                <td>${p.id_mekanik || '-'}</td>
                <td>${badge}</td>
                <td>${p.keterangan || '-'}</td>
                <td>${p.waktu_mulai ? new Date(p.waktu_mulai).toLocaleString('id-ID') : '-'}</td>
                <td>${p.waktu_selesai ? new Date(p.waktu_selesai).toLocaleString('id-ID') : '-'}</td>
                <td>
                    <button onclick='editProses(${JSON.stringify(p)})' class='btn btn-sm btn-warning'>Edit</button>
                    <button onclick='deleteProses(${p.id_proses})' class='btn btn-sm btn-danger'>Hapus</button>
                </td>
            </tr>`;
    });

    document.getElementById('tableInfo').textContent = `Showing ${start + 1} to ${start + pageData.length} of ${filtered.length} entries`;
}

function renderPagination() {
    const container = document.getElementById('pageNumbers');
    container.innerHTML = '';
    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement('button');
        btn.className = `btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-outline-primary'} mx-1`;
        btn.textContent = i;
        btn.onclick = () => { currentPage = i; renderTable(); };
        container.appendChild(btn);
    }
}

document.getElementById('searchInput').addEventListener('input', () => {
    currentPage = 1; renderTable();
});

loadProses();
</script>
@endsection