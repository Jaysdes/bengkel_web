@extends('layouts.app')

@section('content')
<h4 class="mb-4 text-xl font-bold">Data Sparepart</h4>

<!-- Tombol Toggle Form -->
<button id="toggleFormBtn" class="btn btn-primary mb-3" onclick="toggleForm()">+ Tambah Sparepart</button>

<!-- Form Tambah/Edit -->
<div id="formSparepart" style="display: none;" class="container">
    <div class="card p-4 mb-4">
        <form id="sparepartForm">
            @csrf
            <input type="hidden" name="id_sparepart" id="id_sparepart">

            <div class="mb-3">
                <label for="nama_sparepart" class="form-label">Nama Sparepart</label>
                <input type="text" name="nama_sparepart" id="nama_sparepart" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="harga_beli" class="form-label">Harga Beli</label>
                <input type="number" name="harga_beli" id="harga_beli" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="harga_jual" class="form-label">Harga Jual</label>
                <input type="number" name="harga_jual" id="harga_jual" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="stok" class="form-label">Stok</label>
                <input type="number" name="stok" id="stok" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
            <button type="reset" onclick="resetForm()" class="btn btn-secondary">Reset</button>
        </form>
    </div>
</div>

<!-- Tabel Data -->
<div class="card p-3 mb-4">
    <h5 class="mb-3">DAFTAR SPAREPART</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="sparepartTableBody">
                <!-- Data akan dimuat via JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<script>
    const token = "{{ session('token') }}";
    const apiBase = 'https://apibengkel.up.railway.app/api';

    function toggleForm() {
        const formDiv = document.getElementById('formSparepart');
        const toggleBtn = document.getElementById('toggleFormBtn');
        const visible = formDiv.style.display === 'block';
        if (visible) {
            formDiv.style.display = 'none';
            toggleBtn.innerText = '+ Tambah Sparepart';
        } else {
            formDiv.style.display = 'block';
            toggleBtn.innerText = 'Tutup Form';
        }
    }

    function hideForm() {
        document.getElementById('formSparepart').style.display = 'none';
        document.getElementById('toggleFormBtn').innerText = '+ Tambah Sparepart';
    }

    function resetForm() {
        document.getElementById('sparepartForm').reset();
        document.getElementById('id_sparepart').value = '';
        hideForm();
    }

    async function loadSpareparts() {
        const res = await fetch(`${apiBase}/sparepart`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        const data = await res.json();
        const tbody = document.getElementById('sparepartTableBody');
        tbody.innerHTML = '';
        data.data.forEach((s, i) => {
            const row = `
                <tr>
                    <td>${i + 1}</td>
                    <td>${s.nama_sparepart}</td>
                    <td>${s.harga_beli}</td>
                    <td>${s.harga_jual}</td>
                    <td>${s.stok}</td>
                    <td>
                        <button onclick='editSparepart(${JSON.stringify(s)})' class='btn btn-sm btn-warning'>Edit</button>
                        <button onclick='deleteSparepart(${s.id_sparepart})' class='btn btn-sm btn-danger'>Hapus</button>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    }

    function editSparepart(data) {
        document.getElementById('id_sparepart').value = data.id_sparepart;
        document.getElementById('nama_sparepart').value = data.nama_sparepart;
        document.getElementById('harga_beli').value = data.harga_beli;
        document.getElementById('harga_jual').value = data.harga_jual;
        document.getElementById('stok').value = data.stok;

        document.getElementById('formSparepart').style.display = 'block';
        document.getElementById('toggleFormBtn').innerText = 'Tutup Form';
    }

    async function deleteSparepart(id) {
        if (!confirm('Yakin ingin menghapus data ini?')) return;
        const res = await fetch(`${apiBase}/sparepart/${id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        });
        const result = await res.json();
        alert(result.message);
        loadSpareparts();
    }

    document.getElementById('sparepartForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const payload = {
            nama_sparepart: this.nama_sparepart.value,
            harga_beli: parseFloat(this.harga_beli.value),
            harga_jual: parseFloat(this.harga_jual.value),
            stok: parseInt(this.stok.value)
        };

        const id = this.id_sparepart.value;
        const url = id ? `${apiBase}/sparepart/${id}` : `${apiBase}/sparepart`;
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
            loadSpareparts();
        } else {
            alert('Gagal: ' + result.message);
        }
    });

    loadSpareparts();
</script>
@endsection
