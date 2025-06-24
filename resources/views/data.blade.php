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

            <button type="submit" class="btn btn-success">Simpan</button>
            <button type="reset" onclick="resetForm()" class="btn btn-secondary">Reset</button>
        </form>
    </div>
</div>

<!-- Tabel Data Customer -->
<div class="card p-3 mb-4">
    <h5 class="mb-3">DAFTAR COSTUMER</h5>
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
                    @if($role !== 'customer')
                        <th>Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody id="customerTableBody">
                <!-- Data akan dimuat via JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<script>
    const token = "{{ session('token') }}";
    const apiBase = 'http://localhost:8000/api';
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

        const payload = {
            nama_customer: this.nama_customer.value,
            id_jenis: parseInt(this.id_jenis.value),
            no_kendaraan: this.no_kendaraan.value,
            alamat: this.alamat.value,
            telepon: this.telepon.value
        };

        const id = this.id_customer.value;
        const url = id ? `${apiBase}/customers/${id}` : `${apiBase}/customers`;
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
            this.reset();
            hideForm();
            loadCustomers();
        } else {
            alert('Gagal: ' + result.message);
        }
    });

    loadCustomers();
</script>
@endsection
