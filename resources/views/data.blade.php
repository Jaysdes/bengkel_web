@extends('layouts.app')

@section('content')
@php
    $role = session('user')['role'] ?? '';
@endphp

<h4 class="mb-4 text-xl font-bold">Data Customer & Jenis Kendaraan</h4>

<!-- Form Tambah/Edit -->
<div id="formUser" class="container">
    <div class="card p-4 mb-4">
        <h5 class="mb-3 text-xl font-bold">Form Registrasi</h5>
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

            <!-- Field tanggal masuk dihapus, diisi otomatis oleh JS -->

            <button type="submit" class="btn btn-success">Simpan</button>
            <button type="reset" onclick="resetForm()" class="btn btn-secondary">Reset</button>
        </form>
    </div>
</div>
@if($role !== 'customer')
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
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="customerTableBody"></tbody>
        </table>
    </div>
    <div id="tableInfo" class="mt-2"></div>
    <div id="pageNumbers" class="mt-2"></div>
</div>

@include('layouts.tbbawah')
@endif
<script>
    const token = "{{ session('token') }}";
    const apiBase = 'http://localhost:8001/api';
    const userRole = "{{ session('user')['role'] ?? '' }}";
    let currentPage = 1;
    let totalEntries = 0;
    let totalPages = 0;

    function resetForm() {
        document.getElementById('dataForm').reset();
        document.getElementById('id_customer').value = '';
    }

    function editCustomer(data) {
        document.getElementById('id_customer').value = data.id_customer;
        document.getElementById('nama_customer').value = data.nama_customer;
        document.getElementById('id_jenis').value = data.id_jenis;
        document.getElementById('no_kendaraan').value = data.no_kendaraan;
        document.getElementById('alamat').value = data.alamat;
        document.getElementById('telepon').value = data.telepon;
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

    // Submit form tambah / edit
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
            // otomatis tanggal sekarang jika tambah baru
            tanggal_masuk: id ? undefined : new Date().toISOString().split("T")[0]
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
                alert(result.message || "Data berhasil disimpan");
                form.reset();
                form.id_customer.value = '';
                loadCustomers();
            } else {
                alert('Gagal: ' + (result.message || 'Periksa input data.'));
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan: ' + err.message);
        }
    });

    async function loadCustomers() {
        const perPage = 10;
        const res = await fetch(`${apiBase}/customers`, {
            headers: { Authorization: `Bearer ${token}` }
        });

        const result = await res.json();
        let data = result.data || [];

        // Urutkan terbaru di atas
        data.sort((a, b) => new Date(b.tanggal_masuk) - new Date(a.tanggal_masuk));

        totalEntries = data.length;
        totalPages = Math.ceil(totalEntries / perPage);

        const tbody = document.getElementById('customerTableBody');
        tbody.innerHTML = '';

        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>`;
            document.getElementById('tableInfo').textContent = '';
            return;
        }

        const startIdx = (currentPage - 1) * perPage;
        const pageItems = data.slice(startIdx, startIdx + perPage);

        pageItems.forEach((c, i) => {
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

        const endEntry = Math.min(startIdx + pageItems.length, totalEntries);
        document.getElementById('tableInfo').textContent = `Menampilkan ${startIdx + 1} sampai ${endEntry} dari ${totalEntries} data`;

        renderPagination();
    }

    function renderPagination() {
        const pageContainer = document.getElementById('pageNumbers');
        pageContainer.innerHTML = '';
        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.className = `btn btn-sm mx-1 ${i === currentPage ? 'btn-primary' : 'btn-outline-primary'}`;
            btn.onclick = () => {
                currentPage = i;
                loadCustomers();
            };
            pageContainer.appendChild(btn);
        }
    }

    // Auto-refresh setiap 5 detik
    setInterval(loadCustomers, 5000);

    // Panggil pertama kali
    loadCustomers();
</script>
@endsection
