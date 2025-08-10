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

            <button type="submit" class="btn btn-success">Simpan</button>
            <button type="reset" onclick="resetForm()" class="btn btn-secondary">Reset</button>
        </form>
    </div>
</div>
@include('layouts.tbatas')
<div class="card p-3 mb-4">
    <h5 class="mb-3">DAFTAR SPK</h5>
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
                    @if($role !== 'customer')
                        <th>Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody id="spkTableBody"></tbody>
        </table>
    </div>
</div>
@include('layouts.tbbawah')

<script>
const token = "{{ session('token') }}";
const apiBase = 'http://localhost:8001/api';
const userRole = "{{ session('user')['role'] ?? '' }}";

const jasaMap = {};
const serviceMap = {};
const customerMap = {};


const tableBody = document.getElementById('spkTableBody');
const tableInfo = document.getElementById('tableInfo');
const pageNumbers = document.getElementById('pageNumbers');
const searchInput = document.getElementById('searchInput');

let allSPK = [];        // semua data
let filteredSPK = [];   // hasil pencarian
let currentPage = 1;
let itemsPerPage = 5;   // bisa disesuaikan


function formatTanggal(tgl) {
    const date = new Date(tgl);
    return new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }).format(date);
}

function paginate(array, page, perPage) {
    const start = (page - 1) * perPage;
    return array.slice(start, start + perPage);
}

function updateTableDisplay() {
    const searchQuery = searchInput.value.toLowerCase();
    filteredSPK = allSPK.filter(spk => {
        const jasa = jasaMap[spk.id_jasa]?.toLowerCase() || '';
        const customer = customerMap[spk.id_customer]?.toLowerCase() || '';
        const service = serviceMap[spk.id_service]?.toLowerCase() || '';
        const noKendaraan = spk.no_kendaraan.toLowerCase();
        return jasa.includes(searchQuery) || customer.includes(searchQuery) || service.includes(searchQuery) || noKendaraan.includes(searchQuery);
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
    data.forEach((spk, index) => {
        const jenisName = spk.id_jenis == 1 ? 'Motor' : (spk.id_jenis == 2 ? 'Mobil' : '-');
        const serviceName = serviceMap[spk.id_service] ?? spk.id_service;
        const jasaName = jasaMap[spk.id_jasa] ?? spk.id_jasa;
        const customerName = customerMap[spk.id_customer] ?? spk.id_customer;

        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${(currentPage - 1) * itemsPerPage + index + 1}</td>
            <td>${formatTanggal(spk.tanggal_spk)}</td>
            <td>${serviceName}</td>
            <td>${jasaName}</td>
            <td>${customerName}</td>
            <td>${jenisName}</td>
            <td>${spk.no_kendaraan}</td>
            <td>${spk.keluhan}</td>
            ${userRole !== 'customer' ? `
                <td>
                    <button onclick='editSPK(${JSON.stringify(spk)})' class='btn btn-sm btn-warning'>Edit</button>
                    <button onclick='deleteSPK(${spk.id_spk})' class='btn btn-sm btn-danger'>Hapus</button>
                </td>` : ''
            }
        `;
        tableBody.appendChild(row);
    });
}

function renderPagination(totalItems) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const startItem = totalItems === 0 ? 0 : (currentPage - 1) * itemsPerPage + 1;
    const endItem = Math.min(currentPage * itemsPerPage, totalItems);

    tableInfo.textContent = `Showing ${startItem} to ${endItem} of ${totalItems} entries`;

    pageNumbers.innerHTML = '';
    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement('button');
        btn.className = `btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-light'}`;
        btn.textContent = i;
        btn.onclick = () => { currentPage = i; updateTableDisplay(); };
        pageNumbers.appendChild(btn);
    }
}

function nextPage() {
    const totalPages = Math.ceil(filteredSPK.length / itemsPerPage);
    if (currentPage < totalPages) {
        currentPage++;
        updateTableDisplay();
    }
}

function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        updateTableDisplay();
    }
}

async function loadDropdown(endpoint, elementId, mapObj = null) {
    const res = await fetch(`${apiBase}/${endpoint}`, {
        headers: { Authorization: `Bearer ${token}` }
    });
    const data = await res.json();
    const select = document.getElementById(elementId);
    select.innerHTML = `<option value="">-- Pilih --</option>`;
    data.data.forEach(item => {
        const id = item.id_jasa ?? item.id_customer;
        const name = item.nama_jasa ?? item.nama_customer;
        if (mapObj) mapObj[id] = name;
        select.innerHTML += `<option value="${id}">${name}</option>`;
    });
}

async function loadRadioServices() {
    const res = await fetch(`${apiBase}/jenis_service`, {
        headers: { Authorization: `Bearer ${token}` }
    });
    const data = await res.json();
    const container = document.getElementById('radio_service');
    container.innerHTML = '';
    data.data.forEach(item => {
        serviceMap[item.id_service] = item.jenis_service;
        container.innerHTML += `
            <div class="form-check me-3">
                <input class="form-check-input" type="radio" name="id_service" id="service${item.id_service}" value="${item.id_service}" required>
                <label class="form-check-label" for="service${item.id_service}">${item.jenis_service}</label>
            </div>
        `;
    });
}

async function loadSPKList() {
    const res = await fetch(`${apiBase}/spk`, {
        headers: { Authorization: `Bearer ${token}` }
    });
    const data = await res.json();
    allSPK = data.data;
    updateTableDisplay();
}
// 
function editSPK(spk) {
    document.getElementById('id_spk').value = spk.id_spk;
    document.getElementById('tanggal_spk').value = spk.tanggal_spk.split('T')[0];
    document.querySelector(`input[name="id_service"][value="${spk.id_service}"]`)?.click();
    document.getElementById('id_jasa').value = spk.id_jasa;
    document.getElementById('id_customer').value = spk.id_customer;
    document.getElementById('id_jenis').value = spk.id_jenis;
    document.getElementById('no_kendaraan').value = spk.no_kendaraan;
    document.getElementById('keluhan').value = spk.keluhan;
    document.getElementById('formSpk').style.display = 'block';
    document.getElementById('toggleFormBtn').innerText = 'Tutup Form';
}

async function deleteSPK(id) {
    if (!confirm('Yakin ingin menghapus data ini?')) return;
    const res = await fetch(`${apiBase}/spk/${id}`, {
        method: 'DELETE',
        headers: { Authorization: `Bearer ${token}` }
    });
    const result = await res.json();
    alert(result.message);
    loadSPKList();
}

document.getElementById('spkForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const idService = document.querySelector('input[name="id_service"]:checked')?.value;
    if (!idService) return alert('Pilih jenis service terlebih dahulu.');

    const payload = {
        tanggal_spk: new Date(this.tanggal_spk.value).toISOString(),
        id_service: parseInt(idService),
        id_jasa: parseInt(this.id_jasa.value),
        id_customer: parseInt(this.id_customer.value),
        id_jenis: parseInt(this.id_jenis.value),
        no_kendaraan: this.no_kendaraan.value,
        keluhan: this.keluhan.value
    };

    const id = this.id_spk.value;
    const url = id ? `${apiBase}/spk/${id}` : `${apiBase}/spk`;
    const method = id ? 'PUT' : 'POST';

    const res = await fetch(url, {
        method,
        headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${token}`
        },
        body: JSON.stringify(payload)
    });

    const result = await res.json();
    if (res.ok) {
        alert(result.message);
        loadSPKList();
        this.reset();
        toggleForm();
    } else {
        alert('Gagal: ' + result.message);
    }
});
document.getElementById('id_customer').addEventListener('change', async function () {
    const customerId = this.value;
    if (!customerId) return;
    const res = await fetch(`${apiBase}/customers/${customerId}`, {
        headers: { Authorization: `Bearer ${token}` }
    });
    const data = await res.json();
    if (data.data) {
        document.getElementById('id_jenis').value = data.data.id_jenis;
        document.getElementById('no_kendaraan').value = data.data.no_kendaraan;
        const radio = document.querySelector(`input[name="id_service"][value="${data.data.id_service}"]`);
        if (radio) radio.checked = true;
    }
});

searchInput.addEventListener('input', () => {
    currentPage = 1;
    updateTableDisplay();
});
function toggleForm() {
    const form = document.getElementById('formSpk');
    const button = document.getElementById('toggleFormBtn');

    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
        button.textContent = '- Tutup Form';
    } else {
        form.style.display = 'none';
        button.textContent = '+ Tambah SPK';
        resetForm(); // opsional: reset form saat ditutup
    }
}
function resetForm() {
    document.getElementById('spkForm').reset();
    document.getElementById('id_spk').value = '';
}

// INIT
loadDropdown('jenis_jasa', 'id_jasa', jasaMap);
loadDropdown('customers', 'id_customer', customerMap);
loadRadioServices().then(loadSPKList);
</script>


@endsection
