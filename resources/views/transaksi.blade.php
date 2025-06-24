@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4 text-xl font-bold">Form Transaksi Bengkel</h4>

    <button id="toggleFormBtn" class="btn btn-primary mb-3" onclick="toggleForm()">+ Transaksi Baru</button>

    <div id="formTransaksi" class="card p-4 mb-4" style="display: none;">
        <form id="dataForm">
            @csrf
            <div class="row mb-3">
    <div class="col-md-4">
        <label>Customer</label>
        <select id="id_customer" class="form-control" required></select>
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
                    <select id="id_jasa" class="form-control" required></select>
                </div>
                <div class="col-md-6">
                    <label>Harga Jasa</label>
                    <input type="text" id="harga_jasa" class="form-control" readonly>
                </div>
                <div class="col-md-6 mt-3">
                    <label>Jenis Service</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_service" id="berkala" value="1" required>
                        <label class="form-check-label" for="berkala">Berkala</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_service" id="tidak_berkala" value="2">
                        <label class="form-check-label" for="tidak_berkala">Tidak Berkala</label>
                    </div>
                </div>
            </div>

            <hr><br>
            <h5>Sparepart</h5>
            <div id="sparepart-list">
                <div class="row mb-2 sparepart-item">
                    <div class="col-md-4">
                        <select name="sparepart_id[]" class="form-control sparepart-select"></select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="qty[]" class="form-control" placeholder="Qty" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control harga" placeholder="Harga" readonly>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-danger remove-sparepart">-</button>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-secondary my-2" onclick="addSparepart()">+ Tambah Sparepart</button>

            <div class="row mt-3">
                <div class="col-md-6">
                    <label>Total</label>
                    <input type="text" id="total" class="form-control" readonly>
                </div>
            </div>

            <button type="submit" class="btn btn-success w-100 mt-3">Simpan Transaksi</button>
        </form>
    </div>

    <div class="card p-3 mb-4">
        <h5 class="mb-3">DAFTAR TRANSAKSI</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>No Kendaraan</th>
                        <th>Customer</th>
                        <th>Mekanik</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody id="listTransaksi"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
const token = "{{ session('token') }}";
const apiUrl = 'http://localhost:8000/api/';
let sparepartOptions = '';

document.addEventListener("DOMContentLoaded", function () {
    loadDropdown('customers', 'id_customer');
    loadDropdown('mekanik', 'id_mekanik');
    loadDropdown('jenis_jasa', 'id_jasa');
    fetchSparepartOptions();
    loadTransaksiList();

    document.getElementById('dataForm').addEventListener('submit', handleSubmit);
    document.getElementById('id_jasa').addEventListener('change', calculateTotal);
});

function toggleForm() {
    const formDiv = document.getElementById('formTransaksi');
    const btn = document.getElementById('toggleFormBtn');
    formDiv.style.display = formDiv.style.display === 'block' ? 'none' : 'block';
    btn.innerText = formDiv.style.display === 'block' ? 'Tutup Form' : '+ Transaksi Baru';
}

function fetchSparepartOptions() {
    fetch(apiUrl + 'sparepart')
        .then(res => res.json())
        .then(result => {
            const data = result.data || result;
            sparepartOptions = '<option value="">Pilih Sparepart</option>';
            data.forEach(sp => {
                sparepartOptions += `<option value="${sp.id_sparepart}" data-harga="${sp.harga_jual}">${sp.id_sparepart} - ${sp.nama_sparepart}</option>`;
            });
            document.querySelectorAll('.sparepart-select').forEach(select => {
                select.innerHTML = sparepartOptions;
            });
        });
}

function addSparepart() {
    const container = document.getElementById('sparepart-list');
    const row = document.createElement('div');
    row.className = 'row mb-2 sparepart-item';
    row.innerHTML = `
        <div class="col-md-4">
            <select name="sparepart_id[]" class="form-control sparepart-select">${sparepartOptions}</select>
        </div>
        <div class="col-md-2">
            <input type="number" name="qty[]" class="form-control" placeholder="Qty" required>
        </div>
        <div class="col-md-3">
            <input type="text" class="form-control harga" placeholder="Harga" readonly>
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-danger remove-sparepart">-</button>
        </div>
    `;
    container.appendChild(row);
}

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-sparepart')) {
        e.target.closest('.sparepart-item').remove();
        calculateTotal();
    }
});

document.addEventListener('change', function (e) {
    if (e.target.classList.contains('sparepart-select')) {
        const row = e.target.closest('.sparepart-item');
        const selectedOption = e.target.selectedOptions[0];
        const harga = parseInt(selectedOption.getAttribute('data-harga')) || 0;

        row.querySelector('[name="qty[]"]').value = 1;
        row.querySelector('.harga').value = harga;

        calculateTotal();
    }

    if (e.target.name === 'qty[]') {
        calculateTotal();
    }
});
document.getElementById('id_customer').addEventListener('change', function () {
    const customerId = this.value;
    fetch(apiUrl + 'customers/' + customerId)
        .then(res => res.json())
        .then(result => {
            const cust = result.data || result;
            document.getElementById('no_kendaraan').value = cust.no_kendaraan || '';
            document.getElementById('telp_customer').value = cust.telepon || '';
        });
});

document.getElementById('id_mekanik').addEventListener('change', function () {
    const mekanikId = this.value;
    fetch(apiUrl + 'mekanik/' + mekanikId)
        .then(res => res.json())
        .then(result => {
            const mek = result.data || result;
            document.getElementById('telp_mekanik').value = mek.telepon || '';
        });
});

function loadDropdown(endpoint, elementId) {
    fetch(apiUrl + endpoint)
        .then(res => res.json())
        .then(result => {
            const data = result.data || result;
            const select = document.getElementById(elementId);
            select.innerHTML = '<option value="">Pilih</option>';
            data.forEach(item => {
                const id = item.id || item.id_customer || item.id_mekanik || item.id_jasa;
                let name = item.nama_customer || item.nama_mekanik || item.nama_jasa;
                let harga = item.harga || item.harga_jasa || 0;
                select.innerHTML += `<option value="${id}" data-harga="${harga}">${id} - ${name}</option>`;
            });
        });
}

function calculateTotal() {
    let total = 0;

    const jasaSelect = document.getElementById('id_jasa');
    const hargaJasa = parseInt(jasaSelect.selectedOptions[0]?.getAttribute('data-harga')) || 0;
    document.getElementById('harga_jasa').value = hargaJasa;

    document.querySelectorAll('.sparepart-item').forEach(row => {
        const qty = parseInt(row.querySelector('[name="qty[]"]').value) || 0;
        const harga = parseInt(row.querySelector('.sparepart-select').selectedOptions[0]?.getAttribute('data-harga')) || 0;
        row.querySelector('.harga').value = harga;
        total += harga * qty;
    });

    total += hargaJasa;
    document.getElementById('total').value = total;
}

function handleSubmit(e) {
    e.preventDefault();

    const selectedService = document.querySelector('input[name="jenis_service"]:checked');
    if (!selectedService) {
        alert("Silakan pilih Jenis Service.");
        return;
    }

    const spkData = {
        id_jasa: document.getElementById('id_jasa').value,
        id_service: selectedService.value
    };

    fetch(apiUrl + 'spk/', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(spkData)
    })
    .then(res => res.json())
    .then(spkResult => {
        const transaksiData = {
            id_spk: spkResult.data.id,
            id_customer: document.getElementById('id_customer').value,
            id_mekanik: document.getElementById('id_mekanik').value,
            no_kendaraan: document.getElementById('no_kendaraan').value,
            pemilik: '-',
            telepon: '-',
            total: parseInt(document.getElementById('total').value) || 0
        };

        return fetch(apiUrl + 'transaksi/', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(transaksiData)
        });
    })
    .then(res => res.json())
    .then(transaksiResult => {
        const transaksiId = transaksiResult.data.id;
        const sparepartIds = document.querySelectorAll('[name="sparepart_id[]"]');
        const qtys = document.querySelectorAll('[name="qty[]"]');

        sparepartIds.forEach((spEl, idx) => {
            const harga = parseInt(spEl.selectedOptions[0]?.getAttribute('data-harga')) || 0;
            const qty = parseInt(qtys[idx].value) || 1;
            const data = {
                id_sp: transaksiId,
                id_customer: transaksiResult.data.id_customer,
                id_sparepart: spEl.value,
                qty: qty,
                total: harga * qty
            };

            fetch(apiUrl + 'detail_transaksi/', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
        });

        alert('Transaksi berhasil disimpan!');
        document.getElementById('dataForm').reset();
        toggleForm();
        loadTransaksiList();
        fetchSparepartOptions();
    });
}

function loadTransaksiList() {
    fetch(apiUrl + 'transaksi')
        .then(res => res.json())
        .then(result => {
            const data = result.data || result;
            const tbody = document.getElementById('listTransaksi');
            tbody.innerHTML = '';
            data.forEach(trx => {
                tbody.innerHTML += `
                    <tr>
                        <td>${trx.id}</td>
                        <td>${trx.no_kendaraan}</td>
                        <td>${trx.pemilik}</td>
                        <td>${trx.id_mekanik}</td>
                        <td>${new Date(trx.created_at).toLocaleDateString()}</td>
                    </tr>
                `;
            });
        });
}
</script>
@endsection
