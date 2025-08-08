@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4 text-xl font-bold">Daftar Transaksi</h4>

    <div class="mb-3">
        <a href="{{ url('/transaksi') }}" class="btn btn-primary">+ Tambah Transaksi</a>
    </div>

    @include('layouts.tbatas') <!-- Kontrol Tabel Atas -->

    <div class="card p-3 mb-4">
    <h5 class="mb-3">DAFTAR TRANSAKSI</h5>

    <div class="table-responsive">
        <table id="tabelTransaksi" class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID Transaksi</th>
                    <th>Customer</th>
                    <th>Jenis</th>
                    <th>No Kendaraan</th>
                    <th>Mekanik</th>
                    <th>Harga Jasa</th>
                    <th>Harga Sparepart</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="bodyTransaksi"></tbody>
        </table>
    </div></div>

    @include('layouts.tbbawah') <!-- Kontrol Tabel Bawah -->
</div>

<script>
const apiUrl = 'http://localhost:8001/api/';
const customerMap = {};
const jenisMap = {};
const mekanikMap = {};

let currentPage = 1;
let totalEntries = 0;
let totalPages = 0;

document.addEventListener("DOMContentLoaded", function () {
    loadReferenceData().then(() => {
        tampilkanTabelTransaksi();
    });

    document.getElementById('searchInput').addEventListener('input', function () {
        currentPage = 1;
        tampilkanTabelTransaksi();
    });

    document.getElementById('entriesPerPage').addEventListener('change', function () {
        currentPage = 1;
        tampilkanTabelTransaksi();
    });
});

function tampilkanTabelTransaksi() {
    const limit = parseInt(document.getElementById('entriesPerPage').value);
    const search = document.getElementById('searchInput').value.toLowerCase();

    fetch(`${apiUrl}transaksi`)
        .then(res => res.json())
        .then(result => {
            let data = result.data || [];

            // Filter berdasarkan kata kunci pencarian (cocok dengan nama customer, no kendaraan, dll)
            data = data.filter(row => {
                const customerName = customerMap[row.id_customer] ?? '';
                const jenisName = jenisMap[row.id_jenis] ?? '';
                const mekanikName = mekanikMap[row.id_mekanik] ?? '';
                return (
                    row.no_kendaraan?.toLowerCase().includes(search) ||
                    customerName.toLowerCase().includes(search) ||
                    jenisName.toLowerCase().includes(search) ||
                    mekanikName.toLowerCase().includes(search) ||
                    String(row.total).includes(search)
                );
            });

            totalEntries = data.length;
            totalPages = Math.ceil(totalEntries / limit);
            const startIndex = (currentPage - 1) * limit;
            const dataPaginated = data.slice(startIndex, startIndex + limit);

            const tbody = document.getElementById('bodyTransaksi');
            tbody.innerHTML = '';

            dataPaginated.forEach(row => {
                const status = row.total > 0 ? 'Diproses' : 'Belum Diproses';
                const customerName = customerMap[row.id_customer] ?? row.id_customer;
                const jenisName = jenisMap[row.id_jenis] ?? row.id_jenis;
                const mekanikName = mekanikMap[row.id_mekanik] ?? row.id_mekanik;

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.id_transaksi}</td>
                    <td>${customerName}</td>
                    <td>${jenisName}</td>
                    <td>${row.no_kendaraan}</td>
                    <td>${mekanikName}</td>
                    <td>${row.harga_jasa}</td>
                    <td>${row.harga_sparepart}</td>
                    <td>${row.total}</td>
                    <td>${status}</td>
                `;
                tbody.appendChild(tr);
            });

            const endEntry = Math.min(startIndex + dataPaginated.length, totalEntries);
            document.getElementById('tableInfo').textContent = `Showing ${startIndex + 1} to ${endEntry} of ${totalEntries} entries`;

            renderPagination();
        });
}


function loadReferenceData() {
    return Promise.all([
        fetch(apiUrl + 'customers').then(res => res.json()),
        fetch(apiUrl + 'jenis_kendaraan').then(res => res.json()), 
        fetch(apiUrl + 'mekanik').then(res => res.json()),
    ])
    .then(([customerRes, jenisRes, mekanikRes]) => {
        (customerRes.data || []).forEach(c => customerMap[c.id_customer] = c.nama_customer);
        (jenisRes.data || []).forEach(j => jenisMap[j.id_jenis] = j.jenis_kendaraan);
        (mekanikRes.data || []).forEach(m => mekanikMap[m.id_mekanik] = m.nama_mekanik);
    });
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
            tampilkanTabelTransaksi();
        };
        pageContainer.appendChild(btn);
    }
}

function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        tampilkanTabelTransaksi();
    }
}

function nextPage() {
    if (currentPage < totalPages) {
        currentPage++;
        tampilkanTabelTransaksi();
    }
}
</script>
@endsection
