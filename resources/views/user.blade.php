@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="page-title">
                <i class="fas fa-users mr-3"></i>
                Manajemen User
            </h1>
            <p class="text-gray-400 text-lg">
                Kelola akun pengguna dan hak akses sistem
            </p>
        </div>
        <div class="flex items-center space-x-3 mt-4 lg:mt-0">
            <button id="toggleFormBtn" class="btn-neon" onclick="toggleForm()">
                <i class="fas fa-user-plus mr-2"></i>
                Tambah User
            </button>
        </div>
    </div>

    <!-- Form Tambah/Edit -->
    <div id="formUser" style="display: none;" class="form-neon bg-dark p-6 rounded">
        <div class="flex items-center mb-6">
            <div class="stat-icon w-12 h-12 mr-4">
                <i class="fas fa-user-plus"></i>
            </div>
            <div>
                <h3 class="text-xl font-semibold text-white">Form User</h3>
                <p class="text-gray-400">Tambah atau edit pengguna sistem</p>
            </div>
        </div>
        
        <form id="userForm">
            @csrf
            <input type="hidden" name="id_user" id="id_user">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-300 mb-2">
                        Nama Lengkap <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="nama" id="nama" class="input-neon w-full" required>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                        Email <span class="text-red-400">*</span>
                    </label>
                    <input type="email" name="email" id="email" class="input-neon w-full" required>
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-300 mb-2">
                        Role <span class="text-red-400">*</span>
                    </label>
                    <select name="role" id="role" class="input-neon w-full" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="admin">Admin</option>
                        <option value="keuangan">Keuangan</option>
                        <option value="mekanik">Mekanik</option>
                        <option value="customer">Customer</option>
                        <option value="gudang">Gudang</option>
                    </select>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                        Password <span class="text-red-400" id="pw-required-mark">*</span>
                    </label>
                    <input type="password" name="password" id="password" class="input-neon w-full">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">
                        Konfirmasi Password <span class="text-red-400" id="pwconf-required-mark">*</span>
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="input-neon w-full">
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-6">
                <button type="button" onclick="resetForm()" class="btn-neon">
                    <i class="fas fa-undo mr-2"></i>
                    Reset
                </button>
                <button type="submit" id="saveBtn" class="btn-neon-solid">
                    <i class="fas fa-save mr-2"></i>
                    Simpan
                </button>
            </div>
        </form>
    </div>

    <!-- Table Controls -->
    <div class="form-neon bg-dark p-4 rounded">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center space-x-2">
                <label class="text-sm text-gray-300">Show</label>
                <select id="entriesPerPage" class="input-neon w-20" onchange="onEntriesChange()">
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <label class="text-sm text-gray-300">entries</label>
            </div>

            <div class="flex items-center space-x-2">
                <label class="text-sm text-gray-300">Search:</label>
                <div class="relative">
                    <input type="text" id="searchInput" class="input-neon pl-10" placeholder="Cari user...">
                    <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="table-neon bg-dark p-0 rounded">
        <div class="p-6 border-b border-gray-800">
            <div class="flex items-center">
                <div class="stat-icon w-10 h-10 mr-3">
                    <i class="fas fa-users-cog"></i>
                </div>
                <h3 class="text-lg font-semibold text-white">Daftar User</h3>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="table-dark text-white">
                <thead>
                    <tr>
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4 text-center">Role</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <tr class="bg-dark">
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center space-y-2">
                                <i class="fas fa-users text-4xl text-gray-600"></i>
                                <span>Memuat data user...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="flex justify-between items-center p-6 border-t border-gray-800 ">
            <div id="tableInfo" class="text-sm text-gray-400">Showing 0 to 0 of 0 entries</div>
            <div class="flex items-center space-x-2">
                <button class="btn btn-sm btn-outline-primary" onclick="prevPage()" id="prevBtn">
                    <i class="fas fa-chevron-left"></i>
                    Previous
                </button>
                <div id="pageNumbers" class="flex space-x-1"></div>
                <button class="btn btn-sm btn-outline-primary" onclick="nextPage()" id="nextBtn">
                    Next
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast container -->
<div id="toast" style="position: fixed; right: 20px; bottom: 20px; z-index: 9999;"></div>

<script>
    // CONFIG
    const token = "{{ session('token') }}";
    const apiBase = 'http://localhost:8001/';

    // STATE
    let allData = [];
    let filteredData = [];
    let currentPage = 1;

    // HELPERS
    function showToast(message, type = 'info', duration = 3500) {
        const colors = {
            success: 'background: #bbf7d0; color: #064e3b;',
            error:   'background: #fecaca; color: #7f1d1d;',
            info:    'background: #e2e8f0; color: #0f172a;'
        };
        const wrapper = document.createElement('div');
        wrapper.setAttribute('style', `padding:12px;margin-bottom:8px;border-radius:8px;box-shadow:0 6px 18px rgba(2,6,23,0.4);min-width:220px;${colors[type] || colors.info}`);
        wrapper.innerText = message;
        document.getElementById('toast').appendChild(wrapper);
        setTimeout(() => { wrapper.remove(); }, duration);
    }

    // FORM VISIBILITY & RESET
    function toggleForm() {
        const formDiv = document.getElementById('formUser');
        const toggleBtn = document.getElementById('toggleFormBtn');
        const visible = formDiv.style.display === 'block';
        if (visible) {
            formDiv.style.display = 'none';
            toggleBtn.innerHTML = '<i class="fas fa-user-plus mr-2"></i>Tambah User';
        } else {
            formDiv.style.display = 'block';
            toggleBtn.innerHTML = '<i class="fas fa-times  mr-2"></i>Tutup Form';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }
    function hideForm() {
        document.getElementById('formUser').style.display = 'none';
        document.getElementById('toggleFormBtn').innerHTML = '<i class="fas fa-user-plus mr-2"></i>Tambah User';
    }
    function resetForm() {
        document.getElementById('userForm').reset();
        document.getElementById('id_user').value = '';
        // password required back to true for create
        document.getElementById('password').required = true;
        document.getElementById('password_confirmation').required = true;
        document.getElementById('pw-required-mark').style.display = '';
        document.getElementById('pwconf-required-mark').style.display = '';
    }

    // LOAD DATA
    async function loadUsers() {
        try {
            const res = await fetch(`${apiBase}api/users`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            const json = await res.json();
            allData = json.data || [];
            filteredData = allData.slice(); // copy
            currentPage = 1;
            renderTable();
        } catch (err) {
            console.error('Error loadUsers:', err);
            document.getElementById('userTableBody').innerHTML = `<tr><td colspan="6" class="px-6 py-8 text-center text-red-400">Gagal memuat data user</td></tr>`;
        }
    }

    // RENDER TABLE
    function renderTable() {
        const entriesPerPage = parseInt(document.getElementById('entriesPerPage').value);
        const tbody = document.getElementById('userTableBody');
        tbody.innerHTML = '';

        if (!filteredData || filteredData.length === 0) {
            tbody.innerHTML = `
                <tr class="bg-dark">
                    <td colspan="6" class=" bg-dark px-6 py-8 text-center text-white">
                        <div class="flex flex-col items-center space-y-2">
                            <i class="fas fa-search text-4xl text-white"></i>
                            <span>Tidak ada data user ditemukan</span>
                        </div>
                    </td>
                </tr>`;
            updateTableInfo(0,0,0);
            renderPagination(0);
            return;
        }

        const start = (currentPage - 1) * entriesPerPage;
        const pageItems = filteredData.slice(start, start + entriesPerPage);

        pageItems.forEach((user, i) => {
            const roleColors = { admin: 'bg-red-500/20 text-red-400 border-red-500/30', operator: 'bg-blue-500/20 text-blue-400 border-blue-500/30', mekanik: 'bg-green-500/20 text-green-400 border-green-500/30', customer: 'bg-purple-500/20 text-purple-400 border-purple-500/30' };
            const roleIcons = { admin: 'fa-user-shield', operator: 'fa-user-cog', mekanik: 'fa-user-hard-hat', customer: 'fa-user' };
            const statusBadge = (user.status === 'active' || user.status === 'aktif') 
                ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-500/20 text-green-400 border border-green-500/30"><i class="fas fa-check-circle mr-1"></i>Aktif</span>' 
                : '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-red-500/20 text-red-400 border border-red-500/30"><i class="fas fa-times-circle mr-1"></i>Nonaktif</span>';

            const id = user.id_user || user.id || user.user_id || user.ID;
            const name = user.name || user.nama || user.nama_user || user.namaLengkap || '-';
            const email = user.email || '-';
            const role = user.role || '-';

            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-800/50 transition-colors';
            row.innerHTML = `
                <td class="px-6 py-4 font-medium text-blue-400">${start + i + 1}</td>
                <td class="px-6 py-4 text-gray-300">${escapeHtml(name)}</td>
                <td class="px-6 py-4 text-gray-300">${escapeHtml(email)}</td>
                <td class="px-6 py-4 text-center"><span class="inline-flex items-center px-2 py-1 rounded-full text-xs ${roleColors[role] || 'bg-gray-500/20 text-gray-400 border-gray-500/30'} border"><i class="fas ${roleIcons[role] || 'fa-user'} mr-1"></i>${(role || '').charAt(0).toUpperCase() + (role || '').slice(1)}</span></td>
                <td class="px-6 py-4 text-center">${statusBadge}</td>
                <td class="px-6 py-4 text-center">
                    <div class="flex justify-center space-x-2">
                        <button class="inline-flex items-center px-3 py-1 bg-yellow-500/20 text-yellow-400 rounded-lg hover:bg-yellow-500/30 transition-colors border border-yellow-500/30" onclick='onEditUser("${id}")'>
                            <i class="fas fa-edit text-sm mr-1"></i> Edit
                        </button>
                        <button class="inline-flex items-center px-3 py-1 bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 transition-colors border border-red-500/30" onclick='deleteUser("${id}")'>
                            <i class="fas fa-trash text-sm mr-1"></i> Hapus
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });

        updateTableInfo(start + 1, start + pageItems.length, filteredData.length);
        renderPagination(filteredData.length);
    }

    // pagination helpers
    function renderPagination(totalItems) {
        const entriesPerPage = parseInt(document.getElementById('entriesPerPage').value);
        const totalPages = Math.ceil(totalItems / entriesPerPage);
        const pageContainer = document.getElementById('pageNumbers');
        pageContainer.innerHTML = '';
        document.getElementById('prevBtn').disabled = currentPage === 1;
        document.getElementById('nextBtn').disabled = currentPage === totalPages || totalPages === 0;

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.className = `px-3 py-1 text-sm rounded-lg transition-colors ${i === currentPage ? 'bg-blue-500 text-black neon-glow' : 'bg-gray-800 text-gray-300 hover:bg-gray-700 border border-gray-600'}`;
            btn.onclick = () => { currentPage = i; renderTable(); };
            pageContainer.appendChild(btn);
        }
    }
    function updateTableInfo(start, end, total) { document.getElementById('tableInfo').textContent = total === 0 ? "Showing 0 to 0 of 0 entries" : `Showing ${start} to ${end} of ${total} entries`; }
    function prevPage() { if (currentPage > 1) { currentPage--; renderTable(); } }
    function nextPage() { const entriesPerPage = parseInt(document.getElementById('entriesPerPage').value); const totalPages = Math.ceil(filteredData.length / entriesPerPage); if (currentPage < totalPages) { currentPage++; renderTable(); } }

    // UTILS
    function escapeHtml(unsafe) {
        if (!unsafe && unsafe !== 0) return '';
        return String(unsafe)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function onEntriesChange() {
        currentPage = 1;
        renderTable();
    }

    // edit: fetch single user to fill form
    async function onEditUser(id) {
        try {
            const res = await fetch(`${apiBase}api/users/${id}`, { headers: { Authorization: `Bearer ${token}` } });
            const json = await res.json();
            const u = json.data || json;
            // map possible field names
            const uid = u.id_user || u.id || u.user_id || '';
            const name = u.name || u.nama || u.nama_user || '';
            const email = u.email || '';
            const role = u.role || '';

            document.getElementById('id_user').value = uid;
            document.getElementById('nama').value = name;
            document.getElementById('email').value = email;
            document.getElementById('role').value = role;

            // make password optional while editing
            document.getElementById('password').required = false;
            document.getElementById('password_confirmation').required = false;
            document.getElementById('pw-required-mark').style.display = 'none';
            document.getElementById('pwconf-required-mark').style.display = 'none';

            // open form
            document.getElementById('formUser').style.display = 'block';
            document.getElementById('toggleFormBtn').innerHTML = '<i class="fas fa-times mr-2"></i>Tutup Form';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } catch (err) {
            console.error('Error fetch user:', err);
            showToast('Gagal mengambil data user', 'error');
        }
    }

    async function deleteUser(id) {
        if (!confirm('Yakin ingin menghapus user ini?')) return;
        try {
            const res = await fetch(`${apiBase}api/users/${id}`, {
                method: 'DELETE',
                headers: { Authorization: `Bearer ${token}` }
            });
            const json = await res.json();
            if (res.ok) {
                showToast(json.message || 'User berhasil dihapus', 'success');
                await loadUsers();
            } else {
                showToast(json.message || 'Gagal menghapus user', 'error');
            }
        } catch (err) {
            console.error(err);
            showToast('Terjadi kesalahan saat menghapus user', 'error');
        }
    }

    // submit handler (create || update)
    document.getElementById('userForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const id = document.getElementById('id_user').value;
        const name = document.getElementById('nama').value.trim();
        const email = document.getElementById('email').value.trim();
        const role = document.getElementById('role').value;
        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;

        // basic validation
        if (!name || !email || !role) {
            showToast('Lengkapi semua field yang wajib', 'error');
            return;
        }

        // password rules: required on create; optional on edit
        if (!id && !password) {
            showToast('Password wajib diisi untuk user baru', 'error');
            return;
        }
        if (password && password !== passwordConfirmation) {
            showToast('Password dan konfirmasi tidak sama', 'error');
            return;
        }

        const payload = { name, email, role };
        if (password) payload.password = password;

        // pilih endpoint sesuai create / update
        const url = id ? `${apiBase}api/users/${id}` : `${apiBase}auth/register`;
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

            const json = await res.json();
            if (res.ok) {
                showToast(json.message || (id ? 'User diperbarui' : 'User ditambahkan'), 'success');
                this.reset();
                resetForm();
                hideForm();
                await loadUsers();
            } else {
                showToast(json.message || 'Gagal menyimpan user', 'error');
            }
        } catch (err) {
            console.error('Error submit user:', err);
            showToast('Terjadi kesalahan saat menyimpan user', 'error');
        }
    });

    // search
    document.getElementById('searchInput').addEventListener('input', function() {
        const q = this.value.trim().toLowerCase();
        filteredData = allData.filter(item => {
            const nama = (item.name || item.nama || item.nama_user || '').toString().toLowerCase();
            const email = (item.email || '').toString().toLowerCase();
            const username = (item.username || '').toString().toLowerCase();
            const role = (item.role || '').toString().toLowerCase();
            return nama.includes(q) || email.includes(q) || username.includes(q) || role.includes(q);
        });
        currentPage = 1;
        renderTable();
    });

    // initial load
    document.addEventListener('DOMContentLoaded', () => {
        loadUsers();
    });
</script>
@endsection
