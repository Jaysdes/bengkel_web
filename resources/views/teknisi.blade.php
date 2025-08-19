@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="page-title">
                <i class="fas fa-cash-register mr-3"></i>
                Transaksi Bengkel
            </h1>
            <div class="flex items-center space-y-3 mt-3 lg:mt-6s">
                <a href="{{ url('/daftar-transaksi') }}" class="btn-neon">
                    <i class="fas fa-list"></i>
                    Lihat Daftar Transaksi
                </a>
            </div>
            <p class="text-gray-400 text-lg">Kelola transaksi service kendaraan dengan mudah dan efisien</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 ">
        <!-- Form -->
        <div class="xl:col-span-2 space-y-6">
            <form id="dataForm" class="needs-validation" novalidate>
                @csrf

                <!-- SPK -->
                <div class="form-neon bg-dark">
                    <div class="flex items-center mb-6">
                        <div class="stat-icon w-12 h-12 mr-4">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-white">Pilih SPK</h3>
                            <p class="text-gray-400">Surat Perintah Kerja sebagai dasar transaksi</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                SPK <span class="text-red-400">*</span>
                            </label>
                            <select id="id_spk" class="input-neon w-full" required>
                                <option value="">-- Pilih SPK --</option>
                            </select>
                            <div class="invalid-feedback text-red-400 text-sm mt-1">Silakan pilih SPK</div>
                        </div>
                        <div class="flex items-end">
                            <button type="button" class="btn-neon w-full" id="btnRefreshSPK">
                                <i class="fas fa-sync-alt"></i>
                                Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Customer & Kendaraan -->
                <div class="form-neon bg-dark" id="customerInfo" style="display: none;">
                    <div class="flex items-center mb-6">
                        <div class="stat-icon w-12 h-12 mr-4">
                            <i class="fas fa-car"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-white">Informasi Customer & Kendaraan</h3>
                            <p class="text-gray-400">Data customer dan kendaraan dari SPK</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Customer</label>
                            <select id="id_customer" class="input-neon w-full" disabled>
                                <option value="">Loading...</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">No Kendaraan</label>
                            <div class="relative">
                                <input type="text" id="no_kendaraan" class="input-neon w-full pl-10" readonly>
                                <i class="fas fa-car absolute left-3 top-3.5 text-gray-400"></i>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">No Telepon</label>
                            <div class="relative">
                                <input type="text" id="telp_customer" class="input-neon w-full pl-10" readonly>
                                <i class="fas fa-phone absolute left-3 top-3.5 text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mekanik -->
                <div class="form-neon bg-dark" id="mechanicInfo" style="display: none;">
                    <div class="flex items-center mb-6">
                        <div class="stat-icon w-12 h-12 mr-4">
                            <i class="fas fa-user-cog"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-white">Pilih Mekanik</h3>
                            <p class="text-gray-400">Teknisi yang akan menangani service</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                Mekanik <span class="text-red-400">*</span>
                            </label>
                            <select id="id_mekanik" class="input-neon w-full" required>
                                <option value="">-- Pilih Mekanik --</option>
                            </select>
                            <div class="invalid-feedback text-red-400 text-sm mt-1">Silakan pilih mekanik</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">No Telepon Mekanik</label>
                            <div class="relative">
                                <input type="text" id="telp_mekanik" class="input-neon w-full pl-10" readonly>
                                <i class="fas fa-phone-alt absolute left-3 top-3.5 text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Jasa & Jenis Service -->
                <div class="form-neon bg-dark" id="serviceInfo" style="display: none;">
                    <div class="flex items-center mb-6">
                        <div class="stat-icon w-12 h-12 mr-4">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-white">Informasi Jasa Service</h3>
                            <p class="text-gray-400">Detail layanan yang diberikan</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Jenis Jasa</label>
                                <select id="id_jasa" class="input-neon w-full">
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Harga Jasa</label>
                                <div class="relative">
                                    <input type="text" id="harga_jasa" class="input-neon w-full pl-12" readonly>
                                    <span class="absolute left-3 top-3.5 text-gray-400 font-medium">Rp</span>
                                </div>
                            </div>
                        </div>

                        <!-- Radio kecil & bisa di-klik -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-3">Jenis Service</label>
                            <div class="d-flex align-items-center gap-4">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" style="transform:scale(0.9)" type="radio" name="jenis_service" id="jenis_service_berkala" value="1" required>
                                    <label class="form-check-label text-white text-sm" for="jenis_service_berkala">Berkala</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" style="transform:scale(0.9)" type="radio" name="jenis_service" id="jenis_service_tidak" value="2" required>
                                    <label class="form-check-label text-white text-sm" for="jenis_service_tidak">Tidak Berkala</label>
                                </div>
                            </div>
                            <small class="text-gray-400">Radio dibuat kecil dan bisa di-klik.</small>
                        </div>
                    </div>
                </div>

                <!-- Sparepart -->
                <div class="form-neon bg-dark" id="sparepartSection" style="display: none;">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="stat-icon w-12 h-12 mr-4">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-white">Manajemen Sparepart</h3>
                                <p class="text-gray-400">Tambahkan spare part yang digunakan</p>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger text-gray-400 border-gray-600 hover:bg-gray-700" id="btnClearSpare">
                            <i class="fas fa-trash"></i>
                            Clear All
                        </button>
                    </div>

                    <div class="bg-gray-800/50 p-4 rounded-lg mb-6 border border-gray-700">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-300 mb-2">Pilih Sparepart</label>
                                <select id="id_sparepart" class="input-neon w-full">
                                    <option value="">-- Pilih Sparepart --</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Qty</label>
                                <input type="number" id="qty_sparepart" class="input-neon w-full" min="1" value="1">
                            </div>
                            <div class="md:col-span-2 flex items-end space-x-2">
                                <button type="button" class="btn-neon-solid flex-1" id="btnAddSpare">
                                    <i class="fas fa-plus"></i>
                                    Tambah
                                </button>
                            </div>
                        </div>
                        <div id="sparepartHint" class="text-sm text-cyan-400 mt-2 hidden">
                            <i class="fas fa-info-circle mr-1"></i>
                            <span id="sparepartHintText"></span>
                        </div>
                    </div>

                    <div class="table-neon bg-dark">
                        <table class="table w-full bg-dark text-dark">
                            <thead>
                                <tr>
                                    <th class="px-6 py-4">Nama Sparepart</th>
                                    <th class="px-6 py-4 text-center">Qty</th>
                                    <th class="px-6 py-4 text-right">Harga Satuan</th>
                                    <th class="px-6 py-4 text-right">Subtotal</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="sparepartList">
                                <tr id="noSparepartRow">
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center space-y-2">
                                            <i class="fas fa-info-circle text-2xl text-gray-600"></i>
                                            <span>Belum ada sparepart yang ditambahkan</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Submit -->
                <div class="form-neon bg-dark" id="submitSection" style="display: none;">
                    <div class="flex flex-col sm:flex-row justify-center items-center space-y-3 sm:space-y-0 sm:space-x-4">
                        <button type="submit" class="btn-neon-solid text-lg px-8 py-4" id="submitBtn">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Transaksi
                        </button>
                        <button type="button" class="btn-neon text-lg px-8 py-4" id="btnReset">
                            <i class="fas fa-undo mr-2"></i>
                            Reset Form
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar Ringkasan -->
        <div class="space-y-6">
            <div class="form-neon top-24 bg-dark" id="summaryCard" style="display: none;">
                <div class="flex items-center mb-4">
                    <div class="stat-icon w-10 h-10 mr-3">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Ringkasan Transaksi</h3>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-center pb-2 border-b border-gray-800">
                        <span class="text-gray-400">Biaya Jasa:</span>
                        <strong class="text-white" id="summary-jasa">Rp 0</strong>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b border-gray-800">
                        <span class="text-gray-400">Biaya Sparepart:</span>
                        <strong class="text-white" id="summary-sparepart">Rp 0</strong>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t-2 border-cyan-500">
                        <span class="text-lg font-semibold text-white">Total:</span>
                        <span class="text-2xl font-bold text-white" id="summary-total">Rp 0</span>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-cyan-500/10 rounded-lg border border-cyan-500/30">
                    <div class="flex items-start space-x-2">
                        <i class="fas fa-info-circle text-cyan-400 mt-1"></i>
                        <div class="text-sm text-cyan-200">
                            <strong>Info:</strong> Total terupdate otomatis saat mengubah jasa/sparepart.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress -->
            <div class="form-neon bg-dark">
                <div class="flex items-center mb-4">
                    <div class="stat-icon w-10 h-10 mr-3">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Progress</h3>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Progress Pengisian</span>
                        <span class="font-medium text-white" id="progressPercentage">0%</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-3 overflow-hidden border border-gray-700">
                        <div class="bg-gradient-to-r from-cyan-500 to-blue-600 h-full rounded-full transition-all duration-500 neon-glow" id="progressBar" style="width: 0%"></div>
                    </div>
                    <div class="text-sm text-gray-400" id="progressText">
                        Pilih SPK untuk memulai
                    </div>
                </div>
            </div>

            <!-- Help -->
            <div class="form-neon bg-dark">
                <div class="flex items-center mb-4">
                    <div class="stat-icon w-10 h-10 mr-3">
                        <i class="fas a-question-circle"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Bantuan</h3>
                </div>

                <div class="space-y-3 text-sm text-gray-400">
                    <div class="flex items-start space-x-2">
                        <span class="flex-shrink-0 w-6 h-6 bg-cyan-500/20 text-cyan-400 rounded-full flex items-center justify-center text-xs font-bold border border-cyan-500/30">1</span>
                        <span>Pilih SPK sebagai dasar transaksi</span>
                    </div>
                    <div class="flex items-start space-x-2">
                        <span class="flex-shrink-0 w-6 h-6 bg-cyan-500/20 text-cyan-400 rounded-full flex items-center justify-center text-xs font-bold border border-cyan-500/30">2</span>
                        <span>Pilih mekanik yang akan mengerjakan</span>
                    </div>
                    <div class="flex items-start space-x-2">
                        <span class="flex-shrink-0 w-6 h-6 bg-cyan-500/20 text-cyan-400 rounded-full flex items-center justify-center text-xs font-bold border border-cyan-500/30">3</span>
                        <span>Pilih jenis service (radio) dan atur sparepart jika perlu</span>
                    </div>
                    <div class="flex items-start space-x-2">
                        <span class="flex-shrink-0 w-6 h-6 bg-cyan-500/20 text-cyan-400 rounded-full flex items-center justify-center text-xs font-bold border border-cyan-500/30">4</span>
                        <span>Review ringkasan dan Simpan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-xl dark-card neon-border">
            <div class="modal-body text-center py-8">
                <div class="mb-4">
                    <div class="w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center mx-auto neon-glow">
                        <i class="fas fa-check-circle text-green-400 text-4xl"></i>
                    </div>
                </div>
                <h4 class="text-green-400 mb-3 font-semibold">Transaksi Berhasil!</h4>
                <p class="text-gray-400 mb-6">Transaksi telah berhasil disimpan ke sistem</p>
                <div class="flex flex-col sm:flex-row justify-center space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" class="btn-neon" id="btnNewTrx">
                        <i class="fas fa-plus mr-2"></i>
                        Transaksi Baru
                    </button>
                    <button type="button" class="btn-neon-solid" id="viewInvoiceBtn">
                        <i class="fas fa-receipt mr-2"></i>
                        Lihat Nota
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
// Gunakan token bila API Anda protected
const token = "{{ session('token') }}";
const API_URL = '{{ env('API_URL', 'http://localhost:8001/api') }}';

let sparepartListData = [];
let formProgress = 0;
let lastTransactionId = null;
let currentSPK = null;

function updateProgress(n) {
    formProgress = n;
    document.getElementById('progressBar').style.width = n + '%';
    document.getElementById('progressPercentage').textContent = n + '%';
}
function updateProgressText(text) {
    document.getElementById('progressText').textContent = text;
}

// INIT
document.addEventListener("DOMContentLoaded", function () {
    bindEvents();
    initializeForm();
});

function bindEvents() {
    document.getElementById('id_spk').addEventListener('change', autofillFromSPK);
    document.getElementById('id_jasa').addEventListener('change', onChangeJasa);
    document.getElementById('id_mekanik').addEventListener('change', fetchMekanikPhone);
    document.getElementById('dataForm').addEventListener('submit', handleSubmit);

    document.getElementById('id_sparepart').addEventListener('change', updateSparepartHint);
    document.getElementById('btnAddSpare')?.addEventListener('click', addSparepart);
    document.getElementById('btnClearSpare')?.addEventListener('click', clearSpareparts);
    document.getElementById('btnRefreshSPK')?.addEventListener('click', () => {
        updateProgressText('Memuat ulang SPK...');
        loadSPKDropdown().then(() => updateProgressText('SPK dimuat'));
    });
    document.getElementById('btnNewTrx')?.addEventListener('click', () => {
        resetForm();
        const m = bootstrap.Modal.getInstance(document.getElementById('successModal'));
        m && m.hide();
    });

    // Radio = jenis_service
    document.querySelectorAll('input[name="jenis_service"]').forEach(r => {
        r.addEventListener('change', () => {
            updateProgress(Math.max(formProgress, 75));
            updateProgressText('Jenis service dipilih');
        });
    });

    document.getElementById('btnReset')?.addEventListener('click', resetForm);
}

function initializeForm() {
    Promise.all([
        loadDropdown('customers', 'id_customer'),
        loadDropdown('mekanik', 'id_mekanik'),
        loadDropdown('jenis_jasa', 'id_jasa'),
        loadDropdown('sparepart', 'id_sparepart', true),
        loadSPKDropdown() // penting: option SPK menyertakan data-id-service & data-id-jenis
    ]).then(() => {
        updateProgress(20);
        updateProgressText('Pilih SPK untuk memulai');
    }).catch(err => console.error('Init error:', err));
}

async function loadDropdown(endpoint, elementId) {
    const res = await fetch(`${API_URL}/${endpoint}`, {
        headers: token ? { Authorization: `Bearer ${token}` } : {}
    });
    if (!res.ok) throw new Error(`GET ${endpoint} ${res.status}`);
    const result = await res.json();
    const data = result.data || result || [];
    const select = document.getElementById(elementId);
    select.innerHTML = '<option value="">-- Pilih --</option>';

    data.forEach(item => {
        if (endpoint === 'customers') {
            select.innerHTML += `<option value="${item.id_customer}">${item.nama_customer}</option>`;
        } else if (endpoint === 'mekanik') {
            select.innerHTML += `<option value="${item.id_mekanik}">${item.nama_mekanik}</option>`;
        } else if (endpoint === 'jenis_jasa') {
            select.innerHTML += `<option value="${item.id_jasa}" data-harga="${item.harga_jasa}">${item.nama_jasa} - Rp ${formatCurrency(item.harga_jasa)}</option>`;
        } else if (endpoint === 'sparepart') {
            select.innerHTML += `<option value="${item.id_sparepart}" data-harga="${item.harga_jual}" data-stok="${item.stok}">${item.nama_sparepart} - Rp ${formatCurrency(item.harga_jual)} (Stok: ${item.stok})</option>`;
        }
    });
}

async function loadSPKDropdown() {
    const res = await fetch(`${API_URL}/spk`, {
        headers: token ? { Authorization: `Bearer ${token}` } : {}
    });
    if (!res.ok) throw new Error(`GET spk ${res.status}`);
    const result = await res.json();
    const data = result.data || result || [];
    const select = document.getElementById('id_spk');
    select.innerHTML = '<option value="">-- Pilih SPK --</option>';
    data.forEach(spk => {
        // Tambahkan id_service & id_jenis ke option (UNTUK FIX 400: id_jenis dijamin tersedia)
        select.innerHTML += `<option value="${spk.id_spk}" data-id-service="${spk.id_service}" data-id-jenis="${spk.id_jenis}">
            SPK #${String(spk.id_spk).padStart(4, '0')} - ${spk.keluhan || 'No Description'}
        </option>`;
    });
}

function onChangeJasa() {
    const jasaSelect = document.getElementById('id_jasa');
    const harga = parseInt(jasaSelect.selectedOptions[0]?.getAttribute('data-harga')) || 0;
    document.getElementById('harga_jasa').value = formatCurrency(harga);
    calculateTotal();
}

async function autofillFromSPK() {
    const select = document.getElementById('id_spk');
    const id = select.value;
    if (!id) {
        hideAllSections();
        updateProgress(20);
        updateProgressText('Pilih SPK untuk memulai');
        return;
    }

    updateProgress(40);
    updateProgressText('Memuat data SPK...');

    // Ambil attribute dari option untuk id_service & id_jenis (FIX 400: gunakan untuk payload)
    const opt = select.selectedOptions[0];
    const optService = parseInt(opt?.getAttribute('data-id-service')) || null;

    // Ambil detail SPK (untuk id_customer, id_jasa, no_kendaraan, dan fallback id_jenis)
    const res = await fetch(`${API_URL}/spk/${id}`, {
        headers: token ? { Authorization: `Bearer ${token}` } : {}
    });
    if (!res.ok) {
        alert('Gagal memuat SPK');
        return;
    }
    const result = await res.json();
    const spk = result.data || result;
    currentSPK = spk;

    // Isi field
    document.getElementById('id_customer').value = spk.id_customer;
    document.getElementById('id_jasa').value = spk.id_jasa;
    document.getElementById('no_kendaraan').value = spk.no_kendaraan;

    // Set radio jenis_service sesuai id_service dari SPK
    const svc = optService || spk.id_service || null;
    if (svc) {
        const radio = document.querySelector(`input[name="jenis_service"][value="${svc}"]`);
        if (radio) radio.checked = true;
    }

    // Tampilkan section
    ['customerInfo','mechanicInfo','serviceInfo','sparepartSection','submitSection','summaryCard'].forEach(showSection);

    // Telepon customer
    try {
        const resCust = await fetch(`${API_URL}/customers/${spk.id_customer}`, {
            headers: token ? { Authorization: `Bearer ${token}` } : {}
        });
        if (resCust.ok) {
            const customer = await resCust.json();
            const c = customer.data || customer;
            document.getElementById('telp_customer').value = c.telepon || '';
        }
    } catch (e) {}

    // Harga jasa dari option
    onChangeJasa();

    updateProgress(60);
    updateProgressText('SPK dimuat. Pilih mekanik & cek ringkasan.');
}

function showSection(id) {
    const el = document.getElementById(id);
    el.style.display = 'block';
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    setTimeout(() => {
        el.style.transition = 'all 0.4s ease';
        el.style.opacity = '1';
        el.style.transform = 'translateY(0)';
    }, 50);
}
function hideAllSections() {
    ['customerInfo','mechanicInfo','serviceInfo','sparepartSection','submitSection','summaryCard'].forEach(id => {
        document.getElementById(id).style.display = 'none';
    });
}

async function fetchMekanikPhone() {
    const id = document.getElementById('id_mekanik').value;
    if (!id) return;

    const res = await fetch(`${API_URL}/mekanik/${id}`, {
        headers: token ? { Authorization: `Bearer ${token}` } : {}
    });
    if (res.ok) {
        const result = await res.json();
        const d = result.data || result;
        document.getElementById('telp_mekanik').value = d.telepon || '';
        updateProgress(90);
        updateProgressText('Siap untuk menyimpan transaksi');
    }
}

function updateSparepartHint() {
    const select = document.getElementById('id_sparepart');
    const opt = select.options[select.selectedIndex];
    const hintDiv = document.getElementById('sparepartHint');
    const hintText = document.getElementById('sparepartHintText');

    if (opt && opt.value) {
        const stok = opt.getAttribute('data-stok');
        const harga = opt.getAttribute('data-harga');
        hintText.textContent = `Stok tersedia: ${stok} • Harga: Rp ${formatCurrency(harga)}`;
        hintDiv.classList.remove('hidden');
    } else {
        hintDiv.classList.add('hidden');
    }
}

function addSparepart() {
    const select = document.getElementById('id_sparepart');
    const qty = parseInt(document.getElementById('qty_sparepart').value) || 1;
    const id = select.value;
    if (!id) return alert('Silakan pilih sparepart');

    const nama = select.options[select.selectedIndex].text.split(' - ')[0];
    const harga = parseInt(select.options[select.selectedIndex].getAttribute('data-harga')) || 0;
    const stok = parseInt(select.options[select.selectedIndex].getAttribute('data-stok')) || 0;
    if (qty > stok) return alert(`Qty melebihi stok (${stok})`);

    const idx = sparepartListData.findIndex(x => x.id_sparepart === parseInt(id));
    if (idx !== -1) {
        const newQty = sparepartListData[idx].qty + qty;
        if (newQty > stok) return alert(`Total qty melebihi stok (${stok})`);
        sparepartListData[idx].qty = newQty;
        sparepartListData[idx].subtotal = newQty * harga;
    } else {
        sparepartListData.push({ id_sparepart: parseInt(id), nama, qty, harga, subtotal: qty * harga });
    }
    renderSparepartTable();
    calculateTotal();

    // reset choose
    document.getElementById('id_sparepart').selectedIndex = 0;
    document.getElementById('qty_sparepart').value = 1;
    document.getElementById('sparepartHint').classList.add('hidden');
}

function renderSparepartTable() {
    const tbody = document.getElementById('sparepartList');
    tbody.innerHTML = '';
    if (sparepartListData.length === 0) {
        tbody.innerHTML = `
            <tr id="noSparepartRow">
                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                    <div class="flex flex-col items-center space-y-2">
                        <i class="fas fa-info-circle text-2xl text-gray-600"></i>
                        <span>Belum ada sparepart yang ditambahkan</span>
                    </div>
                </td>
            </tr>`;
        return;
    }
    sparepartListData.forEach((item, index) => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-800/50 transition-colors';
        tr.innerHTML = `
            <td class="px-6 py-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-cyan-500/20 rounded-lg flex items-center justify-center mr-3 neon-glow">
                        <i class="fas fa-cog text-cyan-400 text-sm"></i>
                    </div>
                    <span class="font-medium text-dark">${item.nama}</span>
                </div>
            </td>
            <td class="px-6 py-4 text-center">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-cyan-500/20 text-cyan-400 border border-cyan-500/30">${item.qty}</span>
            </td>
            <td class="px-6 py-4 text-right font-medium text-dark">Rp ${formatCurrency(item.harga)}</td>
            <td class="px-6 py-4 text-right font-bold neon-text">Rp ${formatCurrency(item.subtotal)}</td>
            <td class="px-6 py-4 text-center">
                <button class="inline-flex items-center px-3 py-1 bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 transition-colors border border-red-500/30" onclick="removeSparepartByIndex(${index})" title="Hapus item">
                    <i class="fas fa-trash text-sm"></i>
                </button>
            </td>`;
        tbody.appendChild(tr);
    });
}

function removeSparepartByIndex(index) {
    if (index >= 0 && index < sparepartListData.length) {
        sparepartListData.splice(index, 1);
        renderSparepartTable();
        calculateTotal();
    }
}

function clearSpareparts() {
    if (sparepartListData.length === 0) return;
    if (confirm('Hapus semua sparepart?')) {
        sparepartListData = [];
        renderSparepartTable();
        calculateTotal();
    }
}

function calculateTotal() {
    const hargaJasa = parseInt((document.getElementById('harga_jasa').value || '').replace(/[^\d]/g, '')) || 0;
    const totalSparepart = sparepartListData.reduce((sum, it) => sum + it.subtotal, 0);
    document.getElementById('summary-jasa').textContent = `Rp ${formatCurrency(hargaJasa)}`;
    document.getElementById('summary-sparepart').textContent = `Rp ${formatCurrency(totalSparepart)}`;
    document.getElementById('summary-total').textContent = `Rp ${formatCurrency(hargaJasa + totalSparepart)}`;
}

// SUBMIT: POST /transaksi — FIX 400: pastikan id_jenis terisi valid (1/2), sertakan Authorization bila perlu
async function handleSubmit(e) {
    e.preventDefault();

    const spkSelect = document.getElementById('id_spk');
    const id_spk = parseInt(spkSelect.value || 0);
    const id_mekanik = parseInt(document.getElementById('id_mekanik').value || 0);
    const radio = document.querySelector('input[name="jenis_service"]:checked');
    const jenis_service = radio ? parseInt(radio.value) : 0;

    if (!id_spk || !id_mekanik || !jenis_service) {
        alert('SPK, Mekanik, dan Jenis Service wajib diisi.');
        return;
    }

    // id_customer diambil dari currentSPK (select customer disabled)
    const id_customer = parseInt(currentSPK?.id_customer || 0);
    if (!id_customer) {
        alert('Gagal menyimpan: data customer dari SPK tidak ditemukan.');
        return;
    }

    // id_jenis dari option SPK (fallback dari currentSPK). Jika tidak ada, JANGAN kirim id_jenis agar backend set NULL.
    const opt = spkSelect.selectedOptions[0];
    const attrJenis = parseInt(opt?.getAttribute('data-id-jenis') || 0);
    const id_jenis = attrJenis || parseInt(currentSPK?.id_jenis || 0);

    const harga_jasa = parseInt((document.getElementById('harga_jasa').value || '').replace(/[^\d]/g, '')) || 0;
    const harga_sparepart = (sparepartListData || []).reduce((t, i) => t + (i.subtotal || 0), 0);

    const basePayload = {
        id_spk,
        id_customer,
        no_kendaraan: document.getElementById('no_kendaraan').value || '',
        telepon: document.getElementById('telp_customer').value || '',
        id_mekanik,
        harga_jasa,
        harga_sparepart,
        jenis_service
    };
    // hanya kirim id_jenis jika valid (>0), menghindari 400 akibat tipe/tidak valid
    if (id_jenis && id_jenis > 0) {
        basePayload.id_jenis = id_jenis;
    }

    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    const prev = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';
    updateProgress(95);
    updateProgressText('Menyimpan transaksi...');

    try {
        const res = await fetch(`${API_URL}/transaksi`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                ...(token ? { Authorization: `Bearer ${token}` } : {})
            },
            body: JSON.stringify(basePayload)
        });
        const result = await res.json();
        if (!res.ok) throw new Error(result.message || `Gagal (HTTP ${res.status})`);

        lastTransactionId = (result.data && result.data.id_transaksi) || result.id_transaksi;
        updateProgress(100);
        updateProgressText('Transaksi berhasil disimpan!');

        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
        document.getElementById('viewInvoiceBtn').onclick = function() {
            window.location.href = `/nota/${lastTransactionId}`;
        };
    } catch (err) {
        console.error('POST /transaksi error:', err);
        alert('Gagal menyimpan transaksi: ' + err.message);
        updateProgress(80);
        updateProgressText('Gagal menyimpan, periksa data');
    } finally {
        btn.disabled = false;
        btn.innerHTML = prev;
    }
}

function resetForm() {
    document.getElementById('dataForm').reset();
    sparepartListData = [];
    renderSparepartTable();
    calculateTotal();
    hideAllSections();
    updateProgress(20);
    updateProgressText('Pilih SPK untuk memulai');
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID').format(amount || 0);
}
</script>

@endsection