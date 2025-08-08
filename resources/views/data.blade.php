@extends('layouts.app')

@section('content')
@php
    $role = session('user')['role'] ?? '';
@endphp

<h4 class="mb-4 text-xl font-bold mb-4">Data Customer & Jenis Kendaraan</h4>

<!-- Tombol Toggle Form -->
<button id="toggleFormBtn" class="btn btn-primary mb-3" onclick="toggleForm()">+ Registrasi Customer</button>

<!-- Form Tambah/Edit -->
<div id="formUser" style="display: none;" class="container">
    <div class="card p-4 mb-4">
        <form id="dataForm">
            @csrf
            <input type="hidden" name="id_customer" id="id_customer">

            <div class="mb-3">
                <label for="id_jenis" class="form-label">Jenis Kendaraan</label>
                <select name="id_jenis" id="id_jenis" class="form-control" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="1">Motor</option>
                    <option value="2">Mobil</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="nama_customer" class="form-label">Nama Customer</label>
                <input type="text" name="nama_customer" id="nama_customer" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="no_kendaraan" class="form-label">No Kendaraan</label>
                <input type="text" name="no_kendaraan" id="no_kendaraan" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <input type="text" name="alamat" id="alamat" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="telepon" class="form-label">Telepon</label>
                <input type="text" name="telepon" id="telepon" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
            <button type="reset" onclick="resetForm()" class="btn btn-secondary">Reset</button>
        </form>
    </div>
</div>

@include('layouts.tbatas')

<!-- Tabel Data Customer -->
<div class="card p-3 mb-4">
    <h5 class="mb-3">DAFTAR CUSTOMER</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>No Kendaraan</th>
                    <th>Jenis Kendaraan</th>
                    <th>Alamat</th>
                    <th>Telepon</th>
                    <th>Tanggal Masuk</th>
                    @if($role !== 'customer')
                        <th>Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody id="customerTableBody"></tbody>
        </table>
    </div>
</div>
@include('layouts.tbbawah')


<script> 
    const token = "{{ session('token') }}";
    const apiBase = 'http://localhost:8001/api';
    const userRole = "{{ session('user')['role'] ?? '' }}";

    function toggleForm() {
        const formDiv = document.getElementById('formUser');
        const toggleBtn = document.getElementById('toggleFormBtn');
        const visible = formDiv.style.display === 'block';
        formDiv.style.display = visible ? 'none' : 'block';
        toggleBtn.innerText = visible ? '+ Registrasi Customer' : 'Tutup Form';
    }

    function hideForm() {
        document.getElementById('formUser').style.display = 'none';
        document.getElementById('toggleFormBtn').innerText = '+ Registrasi Customer';
    }

    function resetForm() {
        document.getElementById('dataForm').reset();
        document.getElementById('id_customer').value = '';
        hideForm();
    }

    async function loadCustomers() {
        const res = await fetch(`${apiBase}/customers`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        const data = await res.json();
        const tbody = document.getElementById('customerTableBody');
        tbody.innerHTML = '';
        data.data.forEach((c, i) => {
            const jenis = c.id_jenis == 1 ? 'Motor' : 'Mobil';
            const tanggalMasuk = c.tanggal_masuk ? new Date(c.tanggal_masuk).toISOString().split('T')[0] : '-';
            let aksi = '';
            if (userRole !== 'customer') {
                aksi = `
                    <td>
                        <button onclick='editCustomer(${JSON.stringify(c)})' class='btn btn-sm btn-warning'>Edit</button>
                        <button onclick='deleteCustomer(${c.id_customer})' class='btn btn-sm btn-danger'>Hapus</button>
                    </td>
                `;
            }
            tbody.innerHTML += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${c.nama_customer}</td>
                    <td>${c.no_kendaraan}</td>
                    <td>${jenis}</td>
                    <td>${c.alamat}</td>
                    <td>${c.telepon}</td>
                    <td>${tanggalMasuk}</td>
                    ${aksi}
                </tr>
            `;
        });
    }

    function editCustomer(data) {
        document.getElementById('id_customer').value = data.id_customer;
        document.getElementById('nama_customer').value = data.nama_customer;
        document.getElementById('id_jenis').value = data.id_jenis;
        document.getElementById('no_kendaraan').value = data.no_kendaraan;
        document.getElementById('alamat').value = data.alamat;
        document.getElementById('telepon').value = data.telepon;
        document.getElementById('tanggal_masuk').value = data.tanggal_masuk?.split('T')[0] ?? '';
        document.getElementById('formUser').style.display = 'block';
        document.getElementById('toggleFormBtn').innerText = 'Tutup Form';
    }

    async function deleteCustomer(id) {
        if (!confirm('Yakin ingin menghapus data ini?')) return;
        const res = await fetch(`${apiBase}/customers/${id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        });
        const result = await res.json();
        alert(result.message);
        loadCustomers();
    }

    document.getElementById('dataForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const form = this;
        const id = form.id_customer.value;
        const method = id ? 'PUT' : 'POST';
        const url = id ? `${apiBase}/customers/${id}` : `${apiBase}/customers`;

        const payload = {
            nama_customer: form.nama_customer.value,
            id_jenis: parseInt(form.id_jenis.value),
            no_kendaraan: form.no_kendaraan.value,
            alamat: form.alamat.value,
            telepon: form.telepon.value,
            tanggal_masuk: new Date(form.tanggal_masuk.value).toISOString()
        };

        try {
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
                form.reset();
                form.id_customer.value = '';
                hideForm(); 
                loadCustomers();
            } else {
                alert('Gagal: ' + (result.message || 'Periksa input data.'));
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan saat menyimpan data.');
        }
    });
let currentPage = 1;
let totalEntries = 0;
let totalPages = 0;

async function loadCustomers() {
    const perPage = parseInt(document.getElementById('entriesPerPage').value);
    const search = document.getElementById('searchInput').value.trim();

    const res = await fetch(`${apiBase}/customers?page=${currentPage}&limit=${perPage}&search=${encodeURIComponent(search)}`, {
        headers: { Authorization: `Bearer ${token}` }
    });

    const result = await res.json();
    const data = result.data || [];
    totalEntries = result.total || data.length;
    totalPages = Math.ceil(totalEntries / perPage);

    const tbody = document.getElementById('customerTableBody');
    tbody.innerHTML = '';

    const startIdx = (currentPage - 1) * perPage;
    data.forEach((c, i) => {
        const jenis = c.id_jenis == 1 ? 'Motor' : 'Mobil';
        const tanggalMasuk = c.tanggal_masuk ? new Date(c.tanggal_masuk).toISOString().split('T')[0] : '-';
        let aksi = '';
        if (userRole !== 'customer') {
            aksi = `
                <td>
                    <button onclick='editCustomer(${JSON.stringify(c)})' class='btn btn-sm btn-warning'>Edit</button>
                    <button onclick='deleteCustomer(${c.id_customer})' class='btn btn-sm btn-danger'>Hapus</button>
                </td>
            `;
        }
        tbody.innerHTML += `
            <tr>
                <td>${startIdx + i + 1}</td>
                <td>${c.nama_customer}</td>
                <td>${c.no_kendaraan}</td>
                <td>${jenis}</td>
                <td>${c.alamat}</td>
                <td>${c.telepon}</td>
                <td>${tanggalMasuk}</td>
                ${aksi}
            </tr>
        `;
    });

    // Update info
    const endEntry = Math.min(startIdx + data.length, totalEntries);
    document.getElementById('tableInfo').textContent = `Showing ${startIdx + 1} to ${endEntry} of ${totalEntries} entries`;

    renderPagination();
}

function renderPagination() {
    const pageContainer = document.getElementById('pageNumbers');
    pageContainer.innerHTML = '';

    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        btn.className = `btn btn-sm mx-1 ${i === currentPage ? 'btn-primary' : 'btn-outline-primary'}`;
        btn.style.borderRadius = '15%';
        btn.onclick = () => {
            currentPage = i;
            loadCustomers();
        };
        pageContainer.appendChild(btn);
    }
}

function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        loadCustomers();
    }
}

function nextPage() {
    if (currentPage < totalPages) {
        currentPage++;
        loadCustomers();
    }
}
document.getElementById('searchInput').addEventListener('input', function () {
    const keyword = this.value.toLowerCase();
    const rows = document.querySelectorAll('#customerTableBody tr');

    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const text = Array.from(cells).map(td => td.textContent.toLowerCase()).join(' ');
        row.style.display = text.includes(keyword) ? '' : 'none';
    });
});


    loadCustomers();
</script>
@endsection
