@extends('layouts.app')
@section('content')
@php
    $role = session('user')['role'] ?? '';
@endphp

<h4 class="mb-4 text-xl font-bold">Data Surat Perintah Kerja (SPK)</h4>

<button id="toggleFormBtn" class="btn btn-primary mb-3" onclick="toggleForm()">+ Tambah SPK</button>

<div id="formSpk" style="display: none;" class="container">
    <div class="card p-4 mb-4">
        <form id="spkForm">
            @csrf
            <input type="hidden" name="id_spk" id="id_spk">

            <div class="mb-3">
                <label for="tanggal_spk" class="form-label">Tanggal SPK</label>
                <input type="date" id="tanggal_spk" name="tanggal_spk" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Jenis Service</label><br>
                <div id="radio_service" class="d-flex flex-wrap gap-3"></div>
            </div>

            <div class="mb-3">
                <label for="id_jasa" class="form-label">Jenis Jasa</label>
                <select id="id_jasa" name="id_jasa" class="form-control" required></select>
            </div>

            <div class="mb-3">
                <label for="id_customer" class="form-label">Customer</label>
                <select id="id_customer" name="id_customer" class="form-control" required></select>
            </div>

            <div class="mb-3">
                <label for="id_jenis" class="form-label">Jenis Kendaraan</label>
                <select id="id_jenis" name="id_jenis" class="form-control" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="1">Motor</option>
                    <option value="2">Mobil</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="no_kendaraan" class="form-label">No Kendaraan</label>
                <input type="text" id="no_kendaraan" name="no_kendaraan" class="form-control" readonly>
            </div>

            <div class="mb-3">
                <label for="keluhan" class="form-label">Keluhan</label>
                <textarea id="keluhan" name="keluhan" class="form-control" rows="2"></textarea>
            </div>

            <!-- Status otomatis di proses mekanik -->
            <div class="mb-3">
                <label class="form-label d-block">Status</label>
                <span id="statusLabel" class="badge bg-warning text-dark">di proses mekanik</span>
                <input type="hidden" id="status" name="status" value="di proses mekanik">
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
            <button type="reset" onclick="resetForm()" class="btn btn-secondary">Reset</button>
        </form>
    </div>
</div>

<div class="card p-3 mb-4">
    <h5 class="mb-3">DAFTAR SPK</h5>
    @include('layouts.tbatas')

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Service</th>
                    <th>Jasa</th>
                    <th>Customer</th>
                    <th>Jenis</th>
                    <th>No Kendaraan</th>
                    <th>Keluhan</th>
                    <th>Status</th>
                    @if($role !== 'customer')
                        <th>Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody id="spkTableBody"></tbody>
        </table>
    </div>

    @include('layouts.tbbawah')
</div>

<script>
/** ====== KONFIG ====== **/
const token = "{{ session('token') }}";
const apiBase = 'https://apibengkel.up.railway.app/api';
const userRole = "{{ session('user')['role'] ?? '' }}";

/** ====== MAP REFERENSI ====== **/
const jasaMap = {};
const serviceMap = {};
const customerMap = {};

/** ====== ELEMENTS ====== **/
const tableBody   = document.getElementById('spkTableBody');
const tableInfo   = document.getElementById('tableInfo') || null;
const pageNumbers = document.getElementById('pageNumbers') || null;
const searchInput = document.getElementById('searchInput') || null;
const entriesPerPageEl = document.getElementById('entriesPerPage') || null;

/** ====== STATE ====== **/
let allSPK = [];
let filteredSPK = [];
let currentPage = 1;
let itemsPerPage = 10;

/** ====== UTIL ====== **/
function safeLower(v) { return (v ?? '').toString().toLowerCase(); }
function safeText(v)  { return (v ?? '').toString(); }

function formatTanggal(tgl) {
    if (!tgl) return '-';
    const date = new Date(tgl);
    if (isNaN(date.getTime())) return '-';
    return new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }).format(date);
}

function paginate(array, page, perPage) {
    const start = (page - 1) * perPage;
    return array.slice(start, start + perPage);
}
function updateStatusBadge(newStatus) {
    const statusInput  = document.getElementById('status');
    const statusLabel  = document.getElementById('statusLabel');

    statusInput.value = newStatus;
    statusLabel.textContent = newStatus;

    // reset dulu semua kelas badge
    statusLabel.className = 'badge';

    if (newStatus.toLowerCase().includes('selesai')) {
        statusLabel.classList.add('bg-success'); // hijau
    } else if (newStatus.toLowerCase().includes('di proses mekanik')) {
        statusLabel.classList.add('bg-warning','text-dark'); // kuning
    } else {
        statusLabel.classList.add('bg-secondary'); // default abu
    }
}

/** ====== RENDERING ====== **/
function updateTableDisplay() {
    const searchQuery = safeLower(searchInput ? searchInput.value : '');

    filteredSPK = (allSPK || []).filter(spk => {
        const jasa = safeLower(jasaMap[spk.id_jasa]);
        const customer = safeLower(customerMap[spk.id_customer]);
        const service = safeLower(serviceMap[spk.id_service]);
        const noKendaraan = safeLower(spk.no_kendaraan);
        const keluhan = safeLower(spk.keluhan);
        const status = safeLower(spk.status);
        const tgl = safeLower(formatTanggal(spk.tanggal_spk));
        return (
            jasa.includes(searchQuery) ||
            customer.includes(searchQuery) ||
            service.includes(searchQuery) ||
            noKendaraan.includes(searchQuery) ||
            keluhan.includes(searchQuery) ||
            status.includes(searchQuery) ||
            tgl.includes(searchQuery)
        );
    });

    const totalItems = filteredSPK.length;
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    if (currentPage > totalPages) currentPage = totalPages || 1;

    const paginatedData = paginate(filteredSPK, currentPage, itemsPerPage);
    renderTable(paginatedData);
    renderPagination(totalItems);
}
function renderTable(data) {
    tableBody.innerHTML = '';

    const start = (currentPage - 1) * itemsPerPage;
    (data || []).forEach((spk, index) => {
        const jenis = spk.id_jenis==1?'Motor':(spk.id_jenis==2?'Mobil':'-');
        const svc   = serviceMap[spk.id_service]  ?? '-';
        const jasa  = spk.id_jasa ? (jasaMap[spk.id_jasa] ?? spk.id_jasa) : '-';
        const cust  = customerMap[spk.id_customer] ?? '-';

        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${start + index + 1}</td>
            <td>${formatTanggal(spk.tanggal_spk)}</td>
            <td>${safeText(svc)}</td>
            <td>${safeText(jasa)}</td>
            <td>${safeText(cust)}</td>
            <td>${safeText(jenis)}</td>
            <td>${safeText(spk.no_kendaraan)}</td>
            <td>${safeText(spk.keluhan)}</td>
            <td>${badge(spk.status)}</td>
            ${userRole !== 'customer' ? `
                <td>
                    <button onclick="editSPK(${spk.id_spk})" class="btn btn-sm btn-warning">Edit</button>
                    <button onclick="deleteSPK(${spk.id_spk})" class="btn btn-sm btn-danger">Hapus</button>
                </td>` : ''
            }
        `;
        tableBody.appendChild(row);
    });
}

function fmtDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    if (isNaN(d)) return '-';
    return d.toLocaleDateString('id-ID'); // format tgl Indonesia
}
function badge(status) {
    if (!status) return '<span class="badge bg-secondary">-</span>';
    const s = status.toLowerCase();
    if (s.includes('selesai')) {
        return `<span class="badge bg-success">${status}</span>`;
    } else if (s.includes('proses')) {
        return `<span class="badge bg-warning text-dark">${status}</span>`;
    } else {
        return `<span class="badge bg-info">${status}</span>`;
    }
}

function renderPagination(totalItems) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const startItem = totalItems === 0 ? 0 : (currentPage - 1) * itemsPerPage + 1;
    const endItem = Math.min(currentPage * itemsPerPage, totalItems);

    if (tableInfo) {
        tableInfo.textContent = `Showing ${startItem} to ${endItem} of ${totalItems} entries`;
    }

    if (!pageNumbers) return;

    pageNumbers.innerHTML = '';
    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement('button');
        btn.className = `btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-light'}`;
        btn.textContent = i;
        btn.onclick = () => { currentPage = i; updateTableDisplay(); };
        pageNumbers.appendChild(btn);
    }
}

/** ====== FETCH HELPERS ====== **/
async function apiGet(url) {
    const res = await fetch(url, { headers: { Authorization: `Bearer ${token}` } });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
        throw new Error(data?.message || `Request gagal (${res.status})`);
    }
    return data;
}

async function apiSend(url, method, payload) {
    const res = await fetch(url, {
        method,
        headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${token}`
        },
        body: JSON.stringify(payload)
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
        // jika API mengirim field "error" (contoh parsing time), tampilkan juga
        const msg = data?.message || data?.error || `Request gagal (${res.status})`;
        throw new Error(msg);
    }
    return data;
}

/** ====== DROPDOWN / RADIO ====== **/
async function loadDropdown(endpoint, elementId, mapObj = null) {
    try {
        const data = await apiGet(`${apiBase}/${endpoint}`);
        const list = Array.isArray(data?.data) ? data.data : [];
        const select = document.getElementById(elementId);
        if (!select) return;

        select.innerHTML = `<option value="">-- Pilih --</option>`;
        list.forEach(item => {
            const id = (item.id_jasa ?? item.id_customer);
            const name = (item.nama_jasa ?? item.nama_customer);
            if (mapObj && id != null) mapObj[id] = name;
            if (id != null && name != null) {
                select.innerHTML += `<option value="${id}">${name}</option>`;
            }
        });
    } catch (err) {
        console.error(err);
    }
}

async function loadRadioServices() {
    try {
        const data = await apiGet(`${apiBase}/jenis_service`);
        const list = Array.isArray(data?.data) ? data.data : [];
        const container = document.getElementById('radio_service');
        if (!container) return;

        container.innerHTML = '';
        list.forEach(item => {
            serviceMap[item.id_service] = item.jenis_service;
            container.innerHTML += `
                <div class="form-check me-3">
                    <input class="form-check-input" type="radio" name="id_service" id="service${item.id_service}" value="${item.id_service}" required>
                    <label class="form-check-label" for="service${item.id_service}">${item.jenis_service}</label>
                </div>
            `;
        });
    } catch (err) {
        console.error(err);
    }
}

/** ====== LIST SPK ====== **/
async function loadSPKList() {
    try {
        const data = await apiGet(`${apiBase}/spk`);
        allSPK = Array.isArray(data?.data) ? data.data : [];
        allSPK.sort((a, b) => (b.id_spk ?? 0) - (a.id_spk ?? 0));
        updateTableDisplay();
    } catch (err) {
        console.error(err);
        alert(`Gagal memuat SPK: ${err.message}`);
    }
}

/** ====== CRUD ====== **/
document.getElementById('spkForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const id_spk = document.getElementById('id_spk').value;
    const id_service_checked = document.querySelector('input[name="id_service"]:checked');

    let statusVal = document.getElementById('status').value || "di proses mekanik";

    // --- Perbaikan krusial: pastikan tanggal dikirim ISO 8601 (RFC3339) dengan "T" ---
    const tanggalInput = document.getElementById('tanggal_spk').value; // format: YYYY-MM-DD
    let tanggalISO = null;
    if (tanggalInput) {
        // set ke 00:00 lokal lalu kirim ISO (API butuh ada huruf T)
        const d = new Date(tanggalInput + 'T00:00:00');
        tanggalISO = d.toISOString();
    }

    const payload = {
        tanggal_spk: tanggalISO,
        id_service: id_service_checked ? parseInt(id_service_checked.value) : null,
        id_jasa: parseInt(document.getElementById('id_jasa').value || 0),
        id_customer: parseInt(document.getElementById('id_customer').value || 0),
        id_jenis: parseInt(document.getElementById('id_jenis').value || 0),
        no_kendaraan: document.getElementById('no_kendaraan').value,
        keluhan: document.getElementById('keluhan').value,
        status: statusVal
    };

    if (!payload.tanggal_spk || !payload.id_service || !payload.id_jasa || !payload.id_customer || !payload.id_jenis) {
        alert('Lengkapi data wajib.');
        return;
    }

    const url = id_spk ? `${apiBase}/spk/${id_spk}` : `${apiBase}/spk`;
    const method = id_spk ? 'PUT' : 'POST';

    try {
        const data = await apiSend(url, method, payload);
        alert(data?.message || 'Sukses');
        resetForm();
        loadSPKList();
        toggleForm();
    } catch (err) {
        console.error(err);
        alert(`Gagal simpan: ${err.message}`);
    }
});

function editSPK(id) {
    const spk = allSPK.find(s => (s.id_spk ?? '').toString() === id.toString());
    if (!spk) {
        alert('Data SPK tidak ditemukan');
        return;
    }
    document.getElementById('id_spk').value = spk.id_spk ?? '';

    // tampilkan di input date (YYYY-MM-DD)
    const tgl = spk.tanggal_spk ? spk.tanggal_spk.toString() : '';
    const tglValue = tgl.includes('T') ? tgl.split('T')[0] : tgl.substring(0, 10);
    document.getElementById('tanggal_spk').value = tglValue || '';

    document.getElementById('id_jasa').value      = spk.id_jasa ?? '';
    document.getElementById('id_customer').value  = spk.id_customer ?? '';
    document.getElementById('id_jenis').value     = spk.id_jenis ?? '';
    document.getElementById('no_kendaraan').value = spk.no_kendaraan ?? '';
    document.getElementById('keluhan').value      = spk.keluhan ?? '';

    // ambil status, default ke "di proses mekanik"
    const statusValue = spk.status ?? "di proses mekanik";
    document.getElementById('status').value = statusValue;

    // update badge sesuai status
    updateStatusBadge(statusValue);

    const radio = document.getElementById(`service${spk.id_service}`);
    if (radio) radio.checked = true;

    const form = document.getElementById('formSpk');
    if (form && (form.style.display === 'none' || form.style.display === '')) {
        toggleForm();
    }
}


async function deleteSPK(id) {
    if (!confirm('Yakin ingin menghapus SPK ini?')) return;
    try {
        const data = await apiSend(`${apiBase}/spk/${id}`, 'DELETE');
        alert(data?.message || 'SPK dihapus');
        loadSPKList();
    } catch (err) {
        console.error(err);
        alert(`Gagal hapus: ${err.message}`);
    }
}

/** ====== AUTOFILL DARI CUSTOMER (tambah logika) ====== **/
document.getElementById('id_customer').addEventListener('change', async function () {
    const customerId = this.value;
    if (!customerId) return;
    try {
        const res = await apiGet(`${apiBase}/customers/${customerId}`);
        const cust = res?.data;
        if (cust) {
            // isi jenis, no_kendaraan, dan set radio service bila ada
            if (cust.id_jenis) document.getElementById('id_jenis').value = cust.id_jenis;
            if (cust.no_kendaraan) document.getElementById('no_kendaraan').value = cust.no_kendaraan;
            if (cust.id_service) {
                const r = document.querySelector(`input[name="id_service"][value="${cust.id_service}"]`);
                if (r) r.checked = true;
            }
        }
    } catch (err) {
        console.error(err);
    }
});

/** ====== EVENTS ====== **/
if (entriesPerPageEl) {
    entriesPerPageEl.addEventListener('change', function () {
        itemsPerPage = parseInt(this.value || '10', 10);
        currentPage = 1;
        updateTableDisplay();
    });
}

if (searchInput) {
    searchInput.addEventListener('input', () => {
        currentPage = 1;
        updateTableDisplay();
    });
}

// dukungan tombol prev/next jika ada di partial
window.nextPage = function () {
    const totalPages = Math.ceil((filteredSPK.length || 0) / itemsPerPage);
    if (currentPage < totalPages) {
        currentPage++;
        updateTableDisplay();
    }
};
window.prevPage = function () {
    if (currentPage > 1) {
        currentPage--;
        updateTableDisplay();
    }
};

/** ====== UI HELPERS ====== **/
function toggleForm() {
    const form = document.getElementById('formSpk');
    const button = document.getElementById('toggleFormBtn');

    if (!form || !button) return;

    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
        button.innerHTML = `<i class="bi bi-dash-circle me-1"></i> Tutup Form`;
    } else {
        form.style.display = 'none';
        button.innerHTML = `<i class="bi bi-plus-circle me-1"></i> Tambah SPK`;
        resetForm();
    }
}

function resetForm() {
    const f = document.getElementById('spkForm');
    if (f) f.reset();

    const id = document.getElementById('id_spk');
    if (id) id.value = '';

    const status = document.getElementById('status');
    const statusLabel = document.getElementById('statusLabel');
    if (status) status.value = "di proses mekanik";
    if (statusLabel) statusLabel.textContent = "di proses mekanik";
}


/** ====== INIT ====== **/
loadDropdown('jenis_jasa', 'id_jasa', jasaMap);
loadDropdown('customers', 'id_customer', customerMap);
loadRadioServices().then(loadSPKList);
</script>
@endsection
