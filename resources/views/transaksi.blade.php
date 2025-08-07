@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4 text-xl font-bold">Form Transaksi Bengkel</h4>
    <!-- Tombol untuk menampilkan transaksi -->
<div class="mb-3">
<a href="{{ url('/daftar-transaksi') }}" class="btn btn-primary">Lihat Daftar Transaksi</a>
</div>

    <div id="formTransaksi" class="card p-4 mb-4">
        <form id="dataForm">
            @csrf

            <div class="row mb-3">
                <div class="col-md-4">
                    <label>ID SPK</label>
                    <select id="id_spk" class="form-control" required>
                        <option value="">Pilih SPK</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Customer</label>
                    <select id="id_customer" class="form-control" required disabled></select>
                </div>
                <div class="col-md-4">
                    <label>No Kendaraan</label>
                    <input type="text" id="no_kendaraan" class="form-control" readonly>
                </div>
                <div class="col-md-4">
                    <label>No Telp Customer</label>
                    <input type="text" id="telp_customer" class="form-control" readonly>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Mekanik</label>
                    <select id="id_mekanik" class="form-control" required></select>
                </div>
                <div class="col-md-4">
                    <label>No Telp Mekanik</label>
                    <input type="text" id="telp_mekanik" class="form-control" readonly>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Jenis Jasa</label>
                    <select id="id_jasa" class="form-control" required disabled></select>
                </div>
                <div class="col-md-6">
                    <label>Harga Jasa</label>
                    <input type="text" id="harga_jasa" class="form-control" readonly>
                </div>
                <div class="col-md-6 mt-3">
                    <label>Jenis Service</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_service" id="berkala" value="1" disabled>
                        <label class="form-check-label" for="berkala">Berkala</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_service" id="tidak_berkala" value="2" disabled>
                        <label class="form-check-label" for="tidak_berkala">Tidak Berkala</label>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Sparepart</label>
                    <select id="id_sparepart" class="form-control">
                        <option value="">Pilih Sparepart</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Qty</label>
                    <input type="number" id="qty_sparepart" class="form-control" min="1" value="1">
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-info w-100" onclick="addSparepart()">+ Tambah</button>
                </div>
            </div>

            <div class="mb-3">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Sparepart</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="sparepartList"></tbody>
                </table>
            </div>

            <div class="row mb-3">
                <div class="col-md-6 offset-md-6">
                    <label>Total Biaya</label>
                    <input type="text" id="total_biaya" class="form-control" readonly>
                </div>
            </div>

<!-- Tombol Simpan & Batal Bersebelahan -->
<div class="d-flex justify-content-start gap-2 mt-4">
    <button type="submit" class="btn btn-success">Simpan Transaksi</button>
    <button type="button" class="btn btn-secondary" onclick="resetForm()">Batal</button>
</div>


        </form>
    </div>
</div>



<script>
const apiUrl = 'http://localhost:8000/api/';
let sparepartListData = [];

document.addEventListener("DOMContentLoaded", function () {
    loadDropdown('customers', 'id_customer');
    loadDropdown('mekanik', 'id_mekanik');
    loadDropdown('jenis_jasa', 'id_jasa');
    loadDropdown('sparepart', 'id_sparepart', true);
    loadSPKDropdown();

    document.getElementById('id_spk').addEventListener('change', autofillFromSPK);
    document.getElementById('id_jasa').addEventListener('change', calculateTotal);
    document.getElementById('id_mekanik').addEventListener('change', fetchMekanikPhone);
    document.getElementById('dataForm').addEventListener('submit', handleSubmit);
});

function loadDropdown(endpoint, elementId, includeHarga = false) {
    fetch(apiUrl + endpoint)
        .then(res => res.json())
        .then(result => {
            const data = result.data || result;
            const select = document.getElementById(elementId);
            select.innerHTML = '<option value="">Pilih</option>';
            data.forEach(item => {
                let option = '';
                if (endpoint === 'customers') {
                    option = `<option value="${item.id_customer}">${item.nama_customer}</option>`;
                } else if (endpoint === 'mekanik') {
                    option = `<option value="${item.id_mekanik}">${item.nama_mekanik}</option>`;
                } else if (endpoint === 'jenis_jasa') {
                    option = `<option value="${item.id_jasa}" data-harga="${item.harga_jasa}">${item.nama_jasa}</option>`;
                } else if (endpoint === 'sparepart') {
                    // Gunakan harga_jual sebagai harga
                    option = `<option value="${item.id_sparepart}" data-harga="${item.harga_jual}">${item.nama_sparepart}</option>`;
                }
                select.innerHTML += option;
            });
        });
}


function loadSPKDropdown() {
    fetch(apiUrl + 'spk')
        .then(res => res.json())
        .then(result => {
            const data = result.data || result;
            const select = document.getElementById('id_spk');
            select.innerHTML = '<option value="">Pilih SPK</option>';
            data.forEach(spk => {
                select.innerHTML += `<option value="${spk.id_spk}">SPK #${spk.id_spk}</option>`;
            });
        });
}

function autofillFromSPK() {
    const id = document.getElementById('id_spk').value;
    if (!id) return;

    fetch(apiUrl + 'spk/' + id)
        .then(res => res.json())
        .then(result => {
            const spk = result.data || result;
            document.getElementById('id_customer').value = spk.id_customer;
            document.getElementById('id_jasa').value = spk.id_jasa;
            document.getElementById('no_kendaraan').value = spk.no_kendaraan;

            fetch(apiUrl + 'customers/' + spk.id_customer)
                .then(res => res.json())
                .then(cust => {
                    document.getElementById('telp_customer').value = cust.data.telepon;
                });

            const jasaSelect = document.getElementById('id_jasa');
            const harga = parseInt(jasaSelect.selectedOptions[0]?.getAttribute('data-harga')) || 0;
            document.getElementById('harga_jasa').value = harga;
            calculateTotal();
        });
}

function fetchMekanikPhone() {
    const id = document.getElementById('id_mekanik').value;
    if (!id) return;
    fetch(apiUrl + 'mekanik/' + id)
        .then(res => res.json())
        .then(result => {
            document.getElementById('telp_mekanik').value = result.data.telepon;
        });
}

function calculateTotal() {
    const hargaJasa = parseInt(document.getElementById('harga_jasa').value) || 0;
    let totalSparepart = 0;

    sparepartListData.forEach(item => {
        const subtotal = (parseInt(item.qty) || 0) * (parseInt(item.harga) || 0);
        item.subtotal = subtotal;
        totalSparepart += subtotal;
    });

    const total = hargaJasa + totalSparepart;
    document.getElementById('total_biaya').value = total;
}

function addSparepart() {
    const select = document.getElementById('id_sparepart');
    const qty = parseInt(document.getElementById('qty_sparepart').value) || 1;
    const id = select.value;
    const nama = select.options[select.selectedIndex].text;
    const harga = parseInt(select.options[select.selectedIndex].getAttribute('data-harga')) || 0;

    if (!id || isNaN(harga)) {
        alert('Sparepart atau harga tidak valid.');
        return;
    }

    const subtotal = qty * harga;

    sparepartListData.push({ id_sparepart: parseInt(id), qty, harga, subtotal });

    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>${nama}</td>
        <td>${qty}</td>
        <td>${harga}</td>
        <td>${subtotal}</td>
        <td><button class="btn btn-sm btn-danger" onclick="this.closest('tr').remove();removeSparepart(${id});calculateTotal()">Hapus</button></td>
    `;
    document.getElementById('sparepartList').appendChild(tr);
    calculateTotal();
}

function removeSparepart(id) {
    sparepartListData = sparepartListData.filter(item => item.id_sparepart !== id);
}

function handleSubmit(e) {
    e.preventDefault();

    const hargaSparepart = sparepartListData.reduce((sum, item) => sum + (item.qty * item.harga), 0);

    const data = {
        id_spk: parseInt(document.getElementById('id_spk').value),
        id_customer: parseInt(document.getElementById('id_customer').value),
        id_jenis: parseInt(document.getElementById('id_jasa').value),
        no_kendaraan: document.getElementById('no_kendaraan').value,
        telepon: document.getElementById('telp_customer').value,
        id_mekanik: parseInt(document.getElementById('id_mekanik').value),
        harga_jasa: parseInt(document.getElementById('harga_jasa').value) || 0,
        harga_sparepart: hargaSparepart,
        total: parseInt(document.getElementById('total_biaya').value) || 0
    };

    fetch(apiUrl + 'transaksi', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(res => {
        if (!res.ok) {
            throw new Error("Gagal submit. Status: " + res.status);
        }
        return res.json();
    })
    .then(res => {
        alert("Transaksi berhasil disimpan!");
        document.getElementById('dataForm').reset();
        document.getElementById('sparepartList').innerHTML = '';
        sparepartListData = [];
        document.getElementById('total_biaya').value = '';
    })
    .catch(err => {
        console.error(err);
        alert("Gagal menyimpan transaksi: " + err.message);
    });
}
//
function resetForm() {
    document.getElementById('dataForm').reset();

    // Kosongkan isian manual
    document.getElementById('telp_customer').value = '';
    document.getElementById('telp_mekanik').value = '';
    document.getElementById('harga_jasa').value = '';
    document.getElementById('total_biaya').value = '';
    document.getElementById('no_kendaraan').value = '';

    // Kosongkan daftar sparepart
    sparepartListData = [];
    document.getElementById('sparepartList').innerHTML = '';

    // Reset dropdown ke posisi default
    const dropdowns = ['id_spk', 'id_customer', 'id_jasa', 'id_mekanik', 'id_sparepart'];
    dropdowns.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.selectedIndex = 0;
    });
}

</script>
@endsection
