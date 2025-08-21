@extends('layouts.app')

@section('content')
<h4 class="mb-4 text-xl font-bold mb-4">Manajemen Mekanik</h4>

<!-- Tombol Toggle Form Mekanik -->
<button id="toggleFormMekanikBtn" class="btn btn-primary mb-3" onclick="toggleFormMekanik()">+ Tambah Mekanik</button>

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
            <button type="reset" onclick="resetFormMekanik()" class="btn btn-secondary">Reset</button>
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

<h4 class="mb-4 text-xl font-bold">Data Jasa</h4>

<!-- Tombol Toggle Form Jasa -->
<button id="toggleFormJasaBtn" class="btn btn-primary mb-3" onclick="toggleFormJasa()">+ Tambah Jasa</button>

<!-- Form Tambah/Edit Jasa -->
<div id="formJasa" style="display: none;" class="container">
    <div class="card p-4 mb-4">
        <form id="jasaForm">
            @csrf
            <input type="hidden" name="id_jasa" id="id_jasa">
            <div class="mb-3">
                <label for="nama_jasa" class="form-label">Nama Jasa</label>
                <input type="text" name="nama_jasa" id="nama_jasa" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="harga_jasa" class="form-label">Harga Jasa</label>
                <input type="number" name="harga_jasa" id="harga_jasa" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Simpan</button>
            <button type="reset" onclick="resetFormJasa()" class="btn btn-secondary">Reset</button>
        </form>
    </div>
</div>

<!-- Kontrol Atas -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        Show 
        <select id="entriesSelect" class="form-select d-inline w-auto mx-1">
            <option value="5">5</option>
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select> 
        entries
    </div>
    <div>
        Search: <input type="text" id="searchInput" class="form-control d-inline w-auto ms-1" placeholder="Cari...">
    </div>
</div>

<!-- Tabel Data Jasa -->
<div class="card p-3 mb-4">
    <h5 class="mb-3">DAFTAR JASA</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama Jasa</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="jasaTableBody">
                <tr><td colspan="4" class="text-center">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Kontrol Bawah -->
<div class="d-flex justify-content-between align-items-center mt-3">
    <div id="tableInfo" class="text-muted small"></div>
    <nav>
        <ul id="pagination" class="pagination mb-0"></ul>
    </nav>
</div>

<script>
    const token = "{{ session('token') }}";
    const apiBase = 'https://apibengkel.up.railway.app/api';

    function toggleFormMekanik() {
        const formDiv = document.getElementById('formMekanik');
        const toggleBtn = document.getElementById('toggleFormMekanikBtn');
        const visible = formDiv.style.display === 'block';
        formDiv.style.display = visible ? 'none' : 'block';
        toggleBtn.innerText = visible ? '+ Tambah Mekanik' : 'Tutup Form';
        if (!visible) formDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function resetFormMekanik() {
        document.getElementById('mekanikForm').reset();
        document.getElementById('mekanikId').value = '';
        document.getElementById('formMekanik').style.display = 'none';
        document.getElementById('toggleFormMekanikBtn').innerText = '+ Tambah Mekanik';
    }

    function toggleFormJasa() {
        const formDiv = document.getElementById('formJasa');
        const toggleBtn = document.getElementById('toggleFormJasaBtn');
        const visible = formDiv.style.display === 'block';
        formDiv.style.display = visible ? 'none' : 'block';
        toggleBtn.innerText = visible ? '+ Tambah Jasa' : 'Tutup Form';
        if (!visible) formDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function resetFormJasa() {
        document.getElementById('jasaForm').reset();
        document.getElementById('id_jasa').value = '';
        document.getElementById('formJasa').style.display = 'none';
        document.getElementById('toggleFormJasaBtn').innerText = '+ Tambah Jasa';
    }

    function editMekanik(data) {
        document.getElementById('mekanikId').value = data.id_mekanik;
        document.getElementById('nama_mekanik').value = data.nama_mekanik;
        document.getElementById('jenis_kelamin').value = data.jenis_kelamin;
        document.getElementById('alamat').value = data.alamat;
        document.getElementById('telepon').value = data.telepon;
        document.getElementById('formMekanik').style.display = 'block';
        document.getElementById('toggleFormMekanikBtn').innerText = 'Tutup Form';
        document.getElementById('formMekanik').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    async function hapusMekanik(id) {
        if (!confirm("Yakin ingin menghapus mekanik ini?")) return;
        try {
            const res = await fetch(`${apiBase}/mekanik/${id}`, {
                method: "DELETE",
                headers: { "Authorization": "Bearer " + token },
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
        const id = document.getElementById('mekanikId').value;
        const data = {
            nama_mekanik: this.nama_mekanik.value,
            jenis_kelamin: this.jenis_kelamin.value,
            alamat: this.alamat.value,
            telepon: this.telepon.value,
        };
        const url = id ? `${apiBase}/mekanik/${id}` : `${apiBase}/mekanik`;
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

    let fullData = [];
    let currentPage = 1;
    let entriesPerPage = 10;
    let searchQuery = "";

    function updateTableInfo(filteredData) {
        const info = document.getElementById("tableInfo");
        const start = (currentPage - 1) * entriesPerPage + 1;
        const end = Math.min(currentPage * entriesPerPage, filteredData.length);
        const total = filteredData.length;
        info.textContent = total === 0 
            ? "Showing 0 to 0 of 0 entries" 
            : `Showing ${start} to ${end} of ${total} entries`;
    }

    function renderPagination(filteredData) {
        const pagination = document.getElementById("pagination");
        pagination.innerHTML = "";
        const totalPages = Math.ceil(filteredData.length / entriesPerPage);
        const createPageItem = (text, page, disabled = false, active = false) => {
            const li = document.createElement("li");
            li.className = `page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}`;
            const a = document.createElement("a");
            a.className = "page-link";
            a.href = "#";
            a.textContent = text;
            a.addEventListener("click", (e) => {
                e.preventDefault();
                if (!disabled) {
                    currentPage = page;
                    renderTable(fullData);
                }
            });
            li.appendChild(a);
            return li;
        };
        pagination.appendChild(createPageItem("Previous", currentPage - 1, currentPage === 1));
        for (let i = 1; i <= totalPages; i++) {
            pagination.appendChild(createPageItem(i, i, false, i === currentPage));
        }
        pagination.appendChild(createPageItem("Next", currentPage + 1, currentPage === totalPages));
    }

    function renderTable(data) {
        const tbody = document.getElementById('jasaTableBody');
        tbody.innerHTML = '';
        const filtered = data
            .filter(item =>
                item.nama_jasa.toLowerCase().includes(searchQuery.toLowerCase()) ||
                String(item.harga_jasa).includes(searchQuery)
            )
            .sort((a, b) => b.id_jasa - a.id_jasa); // urutkan data terbaru di atas
        const start = (currentPage - 1) * entriesPerPage;
        const end = start + entriesPerPage;
        const pageData = filtered.slice(start, end);
        if (pageData.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr>`;
        } else {
            pageData.forEach((jasa, i) => {
                tbody.innerHTML += `
                    <tr>
                        <td>${start + i + 1}</td>
                        <td>${jasa.nama_jasa}</td>
                        <td>${jasa.harga_jasa}</td>
                        <td>
                            <button onclick='editJasa(${JSON.stringify(jasa)})' class='btn btn-sm btn-warning'>Edit</button>
                            <button onclick='deleteJasa(${jasa.id_jasa})' class='btn btn-sm btn-danger'>Hapus</button>
                        </td>
                    </tr>
                `;
            });
        }
        updateTableInfo(filtered);
        renderPagination(filtered);
    }

    async function loadJasas() {
        const tbody = document.getElementById('jasaTableBody');
        tbody.innerHTML = `<tr><td colspan="4" class="text-center">Memuat data...</td></tr>`;
        try {
            const response = await fetch(`${apiBase}/jenis_jasa`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            const json = await response.json();
            fullData = (json.data || []).sort((a, b) => b.id_jasa - a.id_jasa); // terbaru di atas
            currentPage = 1;
            renderTable(fullData);
        } catch (error) {
            console.error('Gagal memuat data:', error);
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Gagal koneksi atau data</td></tr>`;
        }
    }

    function editJasa(data) {
        document.getElementById('id_jasa').value = data.id_jasa;
        document.getElementById('nama_jasa').value = data.nama_jasa;
        document.getElementById('harga_jasa').value = data.harga_jasa;
        document.getElementById('formJasa').style.display = 'block';
        document.getElementById('toggleFormJasaBtn').innerText = 'Tutup Form';
        document.getElementById('formJasa').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    async function deleteJasa(id) {
        if (!confirm('Yakin ingin menghapus jasa ini?')) return;
        try {
            const res = await fetch(`${apiBase}/jenis_jasa/${id}`, {
                method: 'DELETE',
                headers: { Authorization: `Bearer ${token}` }
            });
            const result = await res.json();
            alert(result.message);
            loadJasas();
        } catch (err) {
            alert('Gagal menghapus data');
            console.error(err);
        }
    }

    document.getElementById('jasaForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const payload = {
            nama_jasa: this.nama_jasa.value,
            harga_jasa: parseFloat(this.harga_jasa.value)
        };
        const id = this.id_jasa.value;
        const url = id ? `${apiBase}/jenis_jasa/${id}` : `${apiBase}/jenis_jasa`;
        const method = id ? 'PUT' : 'POST';
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
                this.reset();
                resetFormJasa();
                loadJasas();
            } else {
                alert('Gagal: ' + result.message);
            }
        } catch (err) {
            alert('Kesalahan saat menyimpan data');
            console.error(err);
        }
    });

    document.getElementById('entriesSelect').addEventListener('change', function () {
        entriesPerPage = parseInt(this.value);
        currentPage = 1;
        renderTable(fullData);
    });

    document.getElementById('searchInput').addEventListener('input', function () {
        searchQuery = this.value;
        currentPage = 1;
        renderTable(fullData);
    });

    window.onload = loadJasas;
</script>
@endsection
