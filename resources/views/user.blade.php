@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4 text-xl font-bold">Manajemen Pengguna</h4>

    <!-- Tombol Tambah -->
    <button class="btn btn-primary mb-3" onclick="showForm()">+ Tambah Pengguna</button>

    <!-- Form Tambah/Edit -->
    <div id="formUser" style="display: none;">
        <div class="card p-4 mb-4">
            <form id="userForm">
                @csrf
                <input type="hidden" name="user_id" id="user_id">

                <div class="mb-3">
                    <label>Nama User</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Level User</label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="admin">Admin</option>
                        <option value="mekanik">Mekanik</option>
                        <option value="keuangan">Keuangan</option>
                        <option value="gudang">Gudang</option>
                        <option value="customer">Customer</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Email (Username)</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="mb-3" id="passwordField">
                    <label>Password</label>
                    <input type="password" name="password" id="password" class="form-control">
                </div>

                <button type="button" class="btn btn-secondary" onclick="hideForm()">Kembali</button>
                <button type="submit" class="btn btn-success">Simpan</button>
            </form>
        </div>
    </div>
    @include('layouts.tbatas')
    <!-- Tabel -->
    <div class="card p-3 mb-4">
        <h5 class="mb-3">DAFTAR USER</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $i => $user)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $user['name'] }}</td>
                            <td>{{ $user['email'] }}</td>
                            <td>{{ ucfirst($user['role']) }}</td>
                            <td><span class="badge bg-success">{{ $user['status'] ?? 'Aktif' }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($user['created_at'])->format('d-m-Y') }}</td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm" onclick='editUser(@json($user))'>Edit</button>
                                <form action="{{ route('users.destroy', $user['id']) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@include('layouts.tbbawah')
<script>
const token = "{{ session('token') }}";
const apiBase = "http://localhost:8001/api";

let currentPage = 1;
let totalEntries = 0;
let totalPages = 0;
let allUsers = [];
let filteredUsers = [];

async function loadUsers() {
    const res = await fetch(`${apiBase}/users`, {
        headers: { Authorization: `Bearer ${token}` }
    });
    const result = await res.json();
    allUsers = result.data?.data || result.data || [];
    updateDisplay();
}

function updateDisplay() {
    const perPage = parseInt(document.getElementById('entriesPerPage').value);
    const search = document.getElementById('searchInput').value.trim().toLowerCase();

    filteredUsers = allUsers.filter(u => 
        u.name.toLowerCase().includes(search) ||
        u.email.toLowerCase().includes(search) ||
        u.role.toLowerCase().includes(search)
    );

    totalEntries = filteredUsers.length;
    totalPages = Math.ceil(totalEntries / perPage);
    if (currentPage > totalPages) currentPage = totalPages || 1;

    const startIdx = (currentPage - 1) * perPage;
    const endIdx = startIdx + perPage;
    const usersToShow = filteredUsers.slice(startIdx, endIdx);

    renderTable(usersToShow, startIdx);
    renderPagination();
    renderTableInfo(startIdx, usersToShow.length);
}

function renderTable(users, startIdx) {
    const tbody = document.getElementById('userTableBody');
    tbody.innerHTML = '';
    users.forEach((u, i) => {
        tbody.innerHTML += `
            <tr>
                <td>${startIdx + i + 1}</td>
                <td>${u.name}</td>
                <td>${u.email}</td>
                <td>${u.role.charAt(0).toUpperCase() + u.role.slice(1)}</td>
                <td><span class="badge bg-success">${u.status ?? 'Aktif'}</span></td>
                <td>${new Date(u.created_at).toLocaleDateString('id-ID')}</td>
                <td>
                    <button class='btn btn-sm btn-warning' onclick='editUser(${JSON.stringify(u)})'>Edit</button>
                    <form action='/users/${u.id}' method='POST' style='display:inline;' onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                        @csrf
                        @method('DELETE')
                        <button class='btn btn-sm btn-danger'>Hapus</button>
                    </form>
                </td>
            </tr>
        `;
    });
}

function renderPagination() {
    const container = document.getElementById('pageNumbers');
    container.innerHTML = '';

    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        btn.className = `btn btn-sm mx-1 ${i === currentPage ? 'btn-primary' : 'btn-outline-primary'}`;
        btn.onclick = () => {
            currentPage = i;
            updateDisplay();
        };
        container.appendChild(btn);
    }
}

function renderTableInfo(startIdx, shown) {
    const infoText = totalEntries > 0
        ? `Showing ${startIdx + 1} to ${startIdx + shown} of ${totalEntries} entries`
        : `No entries found`;
    document.getElementById('tableInfo').textContent = infoText;
}

document.getElementById('searchInput').addEventListener('input', () => {
    currentPage = 1;
    updateDisplay();
});

document.getElementById('entriesPerPage').addEventListener('change', () => {
    currentPage = 1;
    updateDisplay();
});

loadUsers();

</script>


@endsection
