@extends('layouts.app')

@section('content')
<h4 class="mb-4 text-xl font-bold mb-4">Manajemen Mekanik</h4>

<!-- Tombol Toggle Form -->
<button id="toggleFormBtn" class="btn btn-primary mb-3" onclick="toggleForm()">+ Tambah Mekanik</button>

<!-- Form Tambah/Edit Mekanik -->
<div id="formMekanik" style="display: none;" class="container">
    <div class="card p-4 mb-4">
        <form id="mekanikForm">
            @csrf
            <input type="hidden" name="id_mekanik" id="mekanikId">
            <div class="mb-3">
                <label for="nama_mekanik" class="form-label">Nama Mekanik</label>
                <input type="text" name="nama_mekanik" id="nama_mekanik" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                <select name="jenis_kelamin" id="jenis_kelamin" class="form-control" required>
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
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

<!-- Tabel Data Mekanik -->
<div class="card p-3 mb-4">
    <h5 class="mb-3">DAFTAR MEKANIK</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Jenis Kelamin</th>
                    <th>Alamat</th>
                    <th>Telepon</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="mekanikTableBody">
                @foreach ($dataMekanik as $i => $mekanik)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $mekanik['nama_mekanik'] }}</td>
                        <td>{{ $mekanik['jenis_kelamin'] }}</td>
                        <td>{{ $mekanik['alamat'] }}</td>
                        <td>{{ $mekanik['telepon'] }}</td>
                        <td>
                            <button onclick='editMekanik(@json($mekanik))' class="btn btn-sm btn-warning">Edit</button>
                            <button onclick='hapusMekanik({{ $mekanik["id_mekanik"] }})' class="btn btn-sm btn-danger">Hapus</button>
                        </td>
                    </tr>
                @endforeach
                @if (empty($dataMekanik))
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-4">Tidak ada data mekanik.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<script>
    const token = "{{ session('token') }}";

    function toggleForm() {
        const formDiv = document.getElementById('formMekanik');
        const toggleBtn = document.getElementById('toggleFormBtn');
        const visible = formDiv.style.display === 'block';
        if (visible) {
            formDiv.style.display = 'none';
            toggleBtn.innerText = '+ Tambah Mekanik';
        } else {
            formDiv.style.display = 'block';
            toggleBtn.innerText = 'Tutup Form';
        }
    }

    function resetForm() {
        document.getElementById('mekanikForm').reset();
        document.getElementById('mekanikId').value = '';
        document.getElementById('formMekanik').style.display = 'none';
        document.getElementById('toggleFormBtn').innerText = '+ Tambah Mekanik';
    }

    function editMekanik(data) {
        document.getElementById('mekanikId').value = data.id_mekanik;
        document.getElementById('nama_mekanik').value = data.nama_mekanik;
        document.getElementById('jenis_kelamin').value = data.jenis_kelamin;
        document.getElementById('alamat').value = data.alamat;
        document.getElementById('telepon').value = data.telepon;

        document.getElementById('formMekanik').style.display = 'block';
        document.getElementById('toggleFormBtn').innerText = 'Tutup Form';
    }

    async function hapusMekanik(id) {
        if (!confirm("Yakin ingin menghapus mekanik ini?")) return;

        try {
            const res = await fetch(`http://localhost:8000/api/mekanik/${id}`, {
                method: "DELETE",
                headers: {
                    "Authorization": "Bearer " + token,
                },
            });
            const result = await res.json();
            alert(result.message || "Berhasil dihapus");
            location.reload();
        } catch (err) {
            console.error(err);
            alert("Terjadi kesalahan saat menghapus.");
        }
    }

    document.getElementById("mekanikForm").addEventListener("submit", async function (e) {
        e.preventDefault();

        const form = e.target;
        const id = document.getElementById('mekanikId').value;

        const data = {
            nama_mekanik: form.nama_mekanik.value,
            jenis_kelamin: form.jenis_kelamin.value,
            alamat: form.alamat.value,
            telepon: form.telepon.value,
        };

        const url = id
            ? `http://localhost:8000/api/mekanik/${id}`
            : `http://localhost:8000/api/mekanik`;

        const method = id ? "PUT" : "POST";

        try {
            const res = await fetch(url, {
                method,
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": "Bearer " + token,
                },
                body: JSON.stringify(data),
            });

            const result = await res.json();

            if (res.ok) {
                alert("Mekanik berhasil disimpan!");
                location.reload();
            } else {
                alert("Gagal simpan: " + result.message);
            }
        } catch (err) {
            console.error(err);
            alert("Terjadi kesalahan saat menyimpan.");
        }
    });
</script>
@endsection
