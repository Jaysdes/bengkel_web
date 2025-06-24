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

<script>
    const token = "{{ session('token') }}";

    function showForm() {
        document.getElementById('formUser').style.display = 'block';
        document.getElementById('userForm').reset();
        document.getElementById('user_id').value = '';
        document.getElementById('password').required = true;
    }

    function hideForm() {
        document.getElementById('formUser').style.display = 'none';
    }

    function editUser(user) {
        showForm();
        document.getElementById('user_id').value = user.id;
        document.getElementById('name').value = user.name;
        document.getElementById('email').value = user.email;
        document.getElementById('role').value = user.role;
        document.getElementById('password').value = '';
        document.getElementById('password').required = false;
    }

    document.getElementById("userForm").addEventListener("submit", async function (e) {
        e.preventDefault();

        const id = document.getElementById('user_id').value;
        const form = e.target;

        const data = {
            name: form.name.value,
            email: form.email.value,
            role: form.role.value,
        };

        if (form.password.value) {
            data.password = form.password.value;
        }

        const url = id
            ? `http://localhost:8000/api/users/${id}`
            : "http://localhost:8000/auth/register";

        const method = id ? "PUT" : "POST";

        try {
            const res = await fetch(url, {
                method: method,
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": "Bearer " + token,
                },
                body: JSON.stringify(data),
            });

            const result = await res.json();

            if (res.ok) {
                alert(id ? "Pengguna berhasil diperbarui!" : "Pengguna berhasil ditambahkan!");
                location.reload();
            } else {
                alert("Gagal: " + (result.message || ''));
            }
        } catch (err) {
            console.error(err);
            alert("Terjadi kesalahan!");
        }
    });
</script>
@endsection
