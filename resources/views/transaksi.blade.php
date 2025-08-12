@extends('layouts.app')

@section('styles')
<style>
/* Print-specific styles to hide header and sidebar */
@media print {
    .fixed, .navbar, .sidebar, nav, header, 
    .w-64, .bg-gray-900, .z-40, .z-10, .z-50,
    [x-data], .hamburger, .btn:not(.print-visible),
    .no-print {
        display: none !important;
    }
    
    body { 
        margin: 0 !important; 
        padding: 0 !important; 
        background: white !important;
        color: black !important;
    }
    
    .main-content {
        margin: 0 !important;
        padding: 20px !important;
        max-width: 100% !important;
        border: none !important;
        border-radius: 0 !important;
        background: white !important;
        color: black !important;
        box-shadow: none !important;
    }
    
    .form-neon, .card {
        background: white !important;
        border: 1px solid #ddd !important;
        box-shadow: none !important;
        color: black !important;
    }
    
    .btn-neon, .btn-neon-solid {
        background: white !important;
        color: black !important;
        border: 1px solid #333 !important;
        box-shadow: none !important;
    }
    
    .input-neon {
        background: white !important;
        color: black !important;
        border: 1px solid #333 !important;
        box-shadow: none !important;
    }
    
    .table-neon {
        background: white !important;
        border: 1px solid #333 !important;
    }
    
    .table-neon th,
    .table-neon td {
        background: white !important;
        color: black !important;
        border: 1px solid #333 !important;
    }
    
    .text-white,
    .neon-text,
    .text-cyan-400,
    .text-blue-400 {
        color: black !important;
    }
    
    .page-title {
        color: black !important;
        background: none !important;
        -webkit-background-clip: initial !important;
        -webkit-text-fill-color: initial !important;
    }
}

/* Additional transaction-specific styles */
.transaction-highlight {
    background: linear-gradient(45deg, rgba(59, 130, 246, 0.1), rgba(59, 130, 246, 0.05));
    border-left: 4px solid var(--neon-blue);
    padding: 1rem;
    margin: 1rem 0;
}

.sparepart-item:hover {
    background-color: rgba(59, 130, 246, 0.1) !important;
    transform: translateX(5px);
    transition: all 0.3s ease;
}
</style>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="page-title">
                <i class="fas fa-cash-register mr-3"></i>
                Transaksi Bengkel
            </h1>
            <p class="text-gray-400 text-lg">
                Kelola transaksi service kendaraan dengan mudah dan efisien
            </p>
        </div>
        <div class="flex items-center space-x-3 mt-4 lg:mt-0">
            <a href="{{ url('/daftar-transaksi') }}" class="btn-neon">
                <i class="fas fa-list"></i>
                Lihat Daftar Transaksi
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 ">
        <!-- Main Form Column -->
        <div class="xl:col-span-2 space-y-6">
            <form id="dataForm" class="needs-validation" novalidate>
                @csrf
                
                <!-- SPK Selection Card -->
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
                            <button type="button" class="btn-neon w-full" onclick="refreshSPK()">
                                <i class="fas fa-sync-alt"></i>
                                Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Customer & Vehicle Info Card -->
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

                <!-- Mechanic Selection Card -->
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

                <!-- Service Information Card -->
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
                                <select id="id_jasa" class="input-neon w-full" disabled>
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
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-3">Jenis Service</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-neon bg-cyan-500/10 neon-border">
                                    <label class="flex items-center p-4 cursor-pointer">
                                        <input type="radio" name="jenis_service" id="berkala" value="1" class="sr-only" disabled>
                                        <div class="w-5 h-5 border-2 border-cyan-400 rounded-full mr-3 flex items-center justify-center">
                                            <div class="w-2 h-2 bg-cyan-400 rounded-full opacity-0 neon-glow" id="berkala-dot"></div>
                                        </div>
                                        <div>
                                            <div class="font-medium text-white">Service Berkala</div>
                                            <div class="text-sm text-gray-400">Maintenance rutin terjadwal</div>
                                        </div>
                                        <i class="fas fa-calendar-check text-cyan-400 ml-auto"></i>
                                    </label>
                                </div>
                                <div class="form-neon bg-orange-500/10 border border-orange-500/30">
                                    <label class="flex items-center p-4 cursor-pointer">
                                        <input type="radio" name="jenis_service" id="tidak_berkala" value="2" class="sr-only" disabled>
                                        <div class="w-5 h-5 border-2 border-orange-400 rounded-full mr-3 flex items-center justify-center">
                                            <div class="w-2 h-2 bg-orange-400 rounded-full opacity-0" id="tidak_berkala-dot"></div>
                                        </div>
                                        <div>
                                            <div class="font-medium text-white">Service Tidak Berkala</div>
                                            <div class="text-sm text-gray-400">Perbaikan khusus/darurat</div>
                                        </div>
                                        <i class="fas fa-wrench text-orange-400 ml-auto"></i>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sparepart Management Card -->
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
                        <button type="button" class="btn btn-sm btn-outline-danger text-gray-400 border-gray-600 hover:bg-gray-700" onclick="clearSpareparts()">
                            <i class="fas fa-trash"></i>
                            Clear All
                        </button>
                    </div>
                    
                    <!-- Add Sparepart Form -->
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
                                <button type="button" class="btn-neon-solid flex-1" onclick="addSparepart()">
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

                    <!-- Sparepart List Table -->
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

                <!-- Submit Actions -->
                <div class="form-neon bg-dark" id="submitSection" style="display: none;">
                    <div class="flex flex-col sm:flex-row justify-center items-center space-y-3 sm:space-y-0 sm:space-x-4">
                        <button type="submit" class="btn-neon-solid text-lg px-8 py-4" id="submitBtn">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Transaksi
                        </button>
                        <button type="button" class="btn-neon text-lg px-8 py-4" onclick="printPreview()">
                            <i class="fas fa-print mr-2"></i>
                            Print Preview
                        </button>
                        <button type="button" class="btn-neon text-lg px-8 py-4" onclick="exportTransaction()">
                            <i class="fas fa-download mr-2"></i>
                            Export
                        </button>
                        <button type="button" class="btn-neon text-lg px-8 py-4" onclick="resetForm()">
                            <i class="fas fa-undo mr-2"></i>
                            Reset Form
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Summary Sidebar -->
        <div class="space-y-6">
            <!-- Transaction Summary -->
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
                            <strong>Info:</strong> Total akan otomatis terupdate saat Anda menambahkan sparepart atau mengubah service.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Indicator -->
            <div class="form-neon bg-dark" >
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

            <!-- Help Section -->
            <div class="form-neon bg-dark">
                <div class="flex items-center mb-4">
                    <div class="stat-icon w-10 h-10 mr-3">
                        <i class="fas fa-question-circle"></i>
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
                        <span>Tambahkan sparepart jika diperlukan</span>
                    </div>
                    <div class="flex items-start space-x-2">
                        <span class="flex-shrink-0 w-6 h-6 bg-cyan-500/20 text-cyan-400 rounded-full flex items-center justify-center text-xs font-bold border border-cyan-500/30">4</span>
                        <span>Review ringkasan dan simpan transaksi</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 rounded-xl dark-card neon-border">
            <div class="modal-body text-center py-6">
                <div class="mb-4">
                    <div class="spinner-border text-cyan-400 neon-glow" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <h5 class="mb-2 text-white">Menyimpan Transaksi</h5>
                <p class="text-gray-400 mb-0">Mohon tunggu sebentar...</p>
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
                    <button type="button" class="btn-neon" onclick="createNewTransaction()">
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
const API_URL = '{{ env('API_URL', 'http://localhost:8001/api') }}';
let sparepartListData = [];
let formProgress = 0;
let lastTransactionId = null;

// Initialize when page loads
document.addEventListener("DOMContentLoaded", function () {
    initializeForm();
    setupEventListeners();
});

function initializeForm() {
    showToast('Memuat data formulir...', 'info');
    
    Promise.all([
        loadDropdown('customers', 'id_customer'),
        loadDropdown('mekanik', 'id_mekanik'),
        loadDropdown('jenis_jasa', 'id_jasa'),
        loadDropdown('sparepart', 'id_sparepart', true),
        loadSPKDropdown()
    ]).then(() => {
        showToast('Formulir siap digunakan', 'success');
        updateProgress(20);
    }).catch(error => {
        showToast('Gagal memuat data formulir', 'error');
        console.error('Error:', error);
    });
}

function setupEventListeners() {
    document.getElementById('id_spk').addEventListener('change', autofillFromSPK);
    document.getElementById('id_jasa').addEventListener('change', calculateTotal);
    document.getElementById('id_mekanik').addEventListener('change', fetchMekanikPhone);
    document.getElementById('dataForm').addEventListener('submit', handleSubmit);
    document.getElementById('id_sparepart').addEventListener('change', updateSparepartHint);
    
    // Radio button listeners
    document.getElementById('berkala').addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('berkala-dot').style.opacity = '1';
            document.getElementById('tidak_berkala-dot').style.opacity = '0';
        }
    });
    
    document.getElementById('tidak_berkala').addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('tidak_berkala-dot').style.opacity = '1';
            document.getElementById('berkala-dot').style.opacity = '0';
        }
    });
}

function loadDropdown(endpoint, elementId, includeHarga = false) {
    return fetch(`${API_URL}/${endpoint}`)
        .then(res => {
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.json();
        })
        .then(result => {
            const data = result.data || result;
            const select = document.getElementById(elementId);
            select.innerHTML = '<option value="">-- Pilih --</option>';
            
            data.forEach(item => {
                let option = '';
                if (endpoint === 'customers') {
                    option = `<option value="${item.id_customer}">${item.nama_customer} - ${item.telepon || 'N/A'}</option>`;
                } else if (endpoint === 'mekanik') {
                    option = `<option value="${item.id_mekanik}">${item.nama_mekanik}</option>`;
                } else if (endpoint === 'jenis_jasa') {
                    option = `<option value="${item.id_jasa}" data-harga="${item.harga_jasa}">${item.nama_jasa} - Rp ${formatCurrency(item.harga_jasa)}</option>`;
                } else if (endpoint === 'sparepart') {
                    option = `<option value="${item.id_sparepart}" data-harga="${item.harga_jual}" data-stok="${item.stok}">${item.nama_sparepart} - Rp ${formatCurrency(item.harga_jual)} (Stok: ${item.stok})</option>`;
                }
                select.innerHTML += option;
            });
        })
        .catch(error => {
            console.error(`Error loading ${endpoint}:`, error);
            showToast(`Gagal memuat data ${endpoint}`, 'error');
        });
}

function loadSPKDropdown() {
    return fetch(`${API_URL}/spk`)
        .then(res => {
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.json();
        })
        .then(result => {
            const data = result.data || result;
            const select = document.getElementById('id_spk');
            select.innerHTML = '<option value="">-- Pilih SPK --</option>';
            data.forEach(spk => {
                select.innerHTML += `<option value="${spk.id_spk}">SPK #${String(spk.id_spk).padStart(4, '0')} - ${spk.keluhan || 'No Description'}</option>`;
            });
        })
        .catch(error => {
            console.error('Error loading SPK:', error);
            showToast('Gagal memuat data SPK', 'error');
        });
}

function autofillFromSPK() {
    const id = document.getElementById('id_spk').value;
    if (!id) {
        hideAllSections();
        return;
    }

    showToast('Memuat data SPK...', 'info');
    updateProgress(40);

    fetch(`${API_URL}/spk/${id}`)
        .then(res => {
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.json();
        })
        .then(result => {
            const spk = result.data || result;
            document.getElementById('id_customer').value = spk.id_customer;
            document.getElementById('id_jasa').value = spk.id_jasa;
            document.getElementById('no_kendaraan').value = spk.no_kendaraan;

            // Show sections with animation - SPK input should work regardless of spare parts availability
            showSection('customerInfo');
            showSection('mechanicInfo');
            showSection('serviceInfo');
            showSection('sparepartSection'); // Always show sparepart section, even if no parts available
            showSection('submitSection');
            showSection('summaryCard');

            // Load customer phone
            return fetch(`${API_URL}/customers/${spk.id_customer}`);
        })
        .then(res => {
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.json();
        })
        .then(customer => {
            document.getElementById('telp_customer').value = customer.data.telepon;

            // Load service price
            const jasaSelect = document.getElementById('id_jasa');
            const harga = parseInt(jasaSelect.selectedOptions[0]?.getAttribute('data-harga')) || 0;
            document.getElementById('harga_jasa').value = formatCurrency(harga);
            
            // Enable service type radio buttons
            document.getElementById('berkala').disabled = false;
            document.getElementById('tidak_berkala').disabled = false;
            document.getElementById('id_jasa').disabled = false;
            
            calculateTotal();
            updateProgress(60);
            showToast('Data SPK berhasil dimuat - Silakan lanjutkan dengan atau tanpa sparepart', 'success');
        })
        .catch(error => {
            showToast('Gagal memuat data SPK', 'error');
            console.error('Error:', error);
        });
}

function showSection(sectionId) {
    const section = document.getElementById(sectionId);
    section.style.display = 'block';
    section.style.opacity = '0';
    section.style.transform = 'translateY(20px)';
    
    setTimeout(() => {
        section.style.transition = 'all 0.5s ease';
        section.style.opacity = '1';
        section.style.transform = 'translateY(0)';
    }, 100);
}

function hideAllSections() {
    const sections = ['customerInfo', 'mechanicInfo', 'serviceInfo', 'sparepartSection', 'submitSection', 'summaryCard'];
    sections.forEach(id => {
        document.getElementById(id).style.display = 'none';
    });
    updateProgress(20);
    updateProgressText('Pilih SPK untuk memulai');
}

function fetchMekanikPhone() {
    const id = document.getElementById('id_mekanik').value;
    if (!id) return;

    updateProgress(80);
    
    fetch(`${API_URL}/mekanik/${id}`)
        .then(res => {
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.json();
        })
        .then(result => {
            document.getElementById('telp_mekanik').value = result.data.telepon;
            updateProgress(90);
            updateProgressText('Siap untuk submit transaksi');
        })
        .catch(error => {
            console.error('Error loading mekanik phone:', error);
        });
}

function updateSparepartHint() {
    const select = document.getElementById('id_sparepart');
    const selectedOption = select.options[select.selectedIndex];
    const hintDiv = document.getElementById('sparepartHint');
    const hintText = document.getElementById('sparepartHintText');
    
    if (selectedOption && selectedOption.value) {
        const stok = selectedOption.getAttribute('data-stok');
        const harga = selectedOption.getAttribute('data-harga');
        hintText.textContent = `Stok tersedia: ${stok} unit, Harga: Rp ${formatCurrency(harga)}`;
        hintDiv.classList.remove('hidden');
    } else {
        hintDiv.classList.add('hidden');
    }
}

function addSparepart() {
    const select = document.getElementById('id_sparepart');
    const qty = parseInt(document.getElementById('qty_sparepart').value) || 1;
    const id = select.value;
    const nama = select.options[select.selectedIndex].text.split(' - ')[0];
    const harga = parseInt(select.options[select.selectedIndex].getAttribute('data-harga')) || 0;
    const stok = parseInt(select.options[select.selectedIndex].getAttribute('data-stok')) || 0;

    if (!id) {
        showToast('Silakan pilih sparepart terlebih dahulu', 'warning');
        return;
    }

    if (qty > stok) {
        showToast(`Qty melebihi stok yang tersedia (${stok})`, 'error');
        return;
    }

    // Check if sparepart already exists
    const existingIndex = sparepartListData.findIndex(item => item.id_sparepart === parseInt(id));
    if (existingIndex !== -1) {
        const newQty = sparepartListData[existingIndex].qty + qty;
        if (newQty > stok) {
            showToast(`Total qty akan melebihi stok yang tersedia (${stok})`, 'error');
            return;
        }
        sparepartListData[existingIndex].qty = newQty;
        sparepartListData[existingIndex].subtotal = newQty * harga;
    } else {
        const subtotal = qty * harga;
        sparepartListData.push({ 
            id_sparepart: parseInt(id), 
            nama,
            qty, 
            harga, 
            subtotal 
        });
    }

    renderSparepartTable();
    calculateTotal();
    
    // Reset form
    document.getElementById('id_sparepart').selectedIndex = 0;
    document.getElementById('qty_sparepart').value = 1;
    document.getElementById('sparepartHint').classList.add('hidden');
    
    showToast('Sparepart berhasil ditambahkan', 'success');
}

function renderSparepartTable() {
    const tbody = document.getElementById('sparepartList');
    const noDataRow = document.getElementById('noSparepartRow');

    // Clear all rows first
    tbody.innerHTML = '';

    if (sparepartListData.length === 0) {
        // Show no data row
        const noDataRowHtml = `
            <tr id="noSparepartRow">
                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                    <div class="flex flex-col items-center space-y-2">
                        <i class="fas fa-info-circle text-2xl text-gray-600"></i>
                        <span>Belum ada sparepart yang ditambahkan</span>
                    </div>
                </td>
            </tr>
        `;
        tbody.innerHTML = noDataRowHtml;
        return;
    }

    // Render all sparepart items
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
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function removeSparepartByIndex(index) {
    if (index >= 0 && index < sparepartListData.length) {
        // Remove item from array
        const removedItem = sparepartListData.splice(index, 1)[0];
        
        // Re-render the table completely
        renderSparepartTable();
        
        // Recalculate total
        calculateTotal();
        
        showToast(`${removedItem.nama} berhasil dihapus`, 'info');
    } else {
        showToast('Gagal menghapus item - index tidak valid', 'error');
    }
}

function clearSpareparts() {
    if (sparepartListData.length === 0) return;
    
    if (confirm('Apakah Anda yakin ingin menghapus semua sparepart?')) {
        sparepartListData = [];
        renderSparepartTable();
        calculateTotal();
        showToast('Semua sparepart dihapus', 'info');
    }
}

function calculateTotal() {
    const hargaJasaText = document.getElementById('harga_jasa').value.replace(/[^\d]/g, '');
    const hargaJasa = parseInt(hargaJasaText) || 0;
    
    let totalSparepart = 0;
    sparepartListData.forEach(item => {
        totalSparepart += item.subtotal;
    });

    const grandTotal = hargaJasa + totalSparepart;

    // Update summary
    document.getElementById('summary-jasa').textContent = `Rp ${formatCurrency(hargaJasa)}`;
    document.getElementById('summary-sparepart').textContent = `Rp ${formatCurrency(totalSparepart)}`;
    document.getElementById('summary-total').textContent = `Rp ${formatCurrency(grandTotal)}`;
}

function updateProgress(percentage) {
    formProgress = percentage;
    const progressBar = document.getElementById('progressBar');
    const progressPercentage = document.getElementById('progressPercentage');
    
    progressBar.style.width = percentage + '%';
    progressPercentage.textContent = percentage + '%';
}

function updateProgressText(text) {
    document.getElementById('progressText').textContent = text;
}

function handleSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    
    // Custom validation - SPK should be submittable even without spare parts
    const requiredFields = ['id_spk', 'id_mekanik'];
    const missingFields = [];
    
    requiredFields.forEach(field => {
        const element = document.getElementById(field);
        if (!element || !element.value.trim()) {
            missingFields.push(field);
            element?.classList.add('is-invalid');
        } else {
            element?.classList.remove('is-invalid');
        }
    });
    
    // Check if jenis service is selected
    const jenisServiceChecked = document.querySelector('input[name="jenis_service"]:checked');
    if (!jenisServiceChecked) {
        missingFields.push('jenis_service');
        showToast('Silakan pilih jenis service (Berkala atau Tidak Berkala)', 'warning');
    }
    
    if (missingFields.length > 0) {
        showToast('Mohon lengkapi field: SPK, Mekanik, dan Jenis Service', 'error');
        updateProgress(70);
        return;
    }
    
    // Show loading modal
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    loadingModal.show();
    
    updateProgress(80);
    updateProgressText('Menyimpan transaksi...');

    // Get form values
    const hargaJasaText = document.getElementById('harga_jasa').value.replace(/[^\d]/g, '');
    const hargaSparepart = sparepartListData.reduce((total, item) => total + item.subtotal, 0);

    const data = {
        id_spk: parseInt(document.getElementById('id_spk').value),
        id_customer: parseInt(document.getElementById('id_customer').value),
        id_jenis: parseInt(document.getElementById('id_jasa').value),
        no_kendaraan: document.getElementById('no_kendaraan').value,
        telepon: document.getElementById('telp_customer').value,
        id_mekanik: parseInt(document.getElementById('id_mekanik').value),
        harga_jasa: parseInt(hargaJasaText) || 0,
        harga_sparepart: hargaSparepart,
        total: parseInt(hargaJasaText) + hargaSparepart,
        jenis_service: jenisServiceChecked.value
    };

    // Submit transaction
    fetch(`${API_URL}/transaksi`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => {
        if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
        return res.json();
    })
    .then(result => {
        const transaksi = result.data;
        lastTransactionId = transaksi.id_transaksi;

        // Process sparepart details if any (optional - not required)
        if (sparepartListData.length > 0) {
            const sparepartPromises = sparepartListData.map(sparepart => {
                return fetch(`${API_URL}/detail_transaksi`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id_transaksi: transaksi.id_transaksi,
                        id_sparepart: sparepart.id_sparepart,
                        qty: sparepart.qty,
                        total: sparepart.subtotal
                    })
                });
            });
            
            return Promise.all(sparepartPromises);
        }
        return Promise.resolve();
    })
    .then(() => {
        // Create process record
        const prosesData = {
            id_transaksi: lastTransactionId,
            id_mekanik: data.id_mekanik,
            status: "dalam_antrian",
            keterangan: sparepartListData.length > 0 ? 
                `Transaksi dengan ${sparepartListData.length} sparepart` : 
                "Transaksi tanpa sparepart - hanya jasa service",
            waktu_mulai: new Date().toISOString()
        };

        return fetch(`${API_URL}/proses`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(prosesData)
        });
    })
    .then(res => {
        loadingModal.hide();
        
        // Show success modal
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
        
        // Setup view invoice button
        document.getElementById('viewInvoiceBtn').onclick = function() {
            window.location.href = `/nota/${lastTransactionId}`;
        };

        updateProgress(100);
        updateProgressText('Transaksi berhasil disimpan!');
        showToast('Transaksi berhasil disimpan - dapat dicetak atau diekspor', 'success');
    })
    .catch(err => {
        loadingModal.hide();
        console.error('Error:', err);
        showToast('Gagal menyimpan transaksi: ' + err.message, 'error');
    });
}

function createNewTransaction() {
    resetForm();
    const successModal = bootstrap.Modal.getInstance(document.getElementById('successModal'));
    successModal.hide();
    showToast('Siap membuat transaksi baru', 'info');
}

function resetForm() {
    document.getElementById('dataForm').reset();
    document.getElementById('dataForm').classList.remove('was-validated');
    sparepartListData = [];
    renderSparepartTable();
    calculateTotal();
    hideAllSections();
    updateProgress(20);
    updateProgressText('Pilih SPK untuk memulai');
    
    // Reset radio button indicators
    document.getElementById('berkala-dot').style.opacity = '0';
    document.getElementById('tidak_berkala-dot').style.opacity = '0';
}

function refreshSPK() {
    showToast('Memuat ulang data SPK...', 'info');
    loadSPKDropdown().then(() => {
        showToast('Data SPK berhasil dimuat ulang', 'success');
    });
}

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID').format(amount || 0);
}

// Print Preview Function - prints without header and sidebar
function printPreview() {
    // Check if there's transaction data to print
    if (!lastTransactionId) {
        showToast('Belum ada transaksi untuk dicetak. Simpan transaksi terlebih dahulu.', 'warning');
        return;
    }

    // Create print window
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    
    // Get current transaction data
    const customer = document.getElementById('id_customer').selectedOptions[0]?.text || '';
    const noKendaraan = document.getElementById('no_kendaraan').value || '';
    const mekanik = document.getElementById('id_mekanik').selectedOptions[0]?.text || '';
    const hargaJasa = document.getElementById('harga_jasa').value || '0';
    
    // Calculate sparepart total
    let sparepartTotal = 0;
    let sparepartItems = '';
    let itemNo = 2; // Start from 2 since jasa is item 1
    
    sparepartListData.forEach(item => {
        sparepartTotal += item.subtotal;
        sparepartItems += `
            <tr>
                <td class="border px-4 py-2 text-center">${itemNo++}</td>
                <td class="border px-4 py-2">${item.nama}</td>
                <td class="border px-4 py-2 text-center">${item.qty}</td>
                <td class="border px-4 py-2 text-right">Rp ${formatCurrency(item.harga)}</td>
                <td class="border px-4 py-2 text-right">Rp ${formatCurrency(item.subtotal)}</td>
            </tr>
        `;
    });
    
    const hargaJasaNum = parseInt(hargaJasa.replace(/[^\d]/g, '')) || 0;
    const grandTotal = hargaJasaNum + sparepartTotal;
    
    // Generate print content
    const printContent = `
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Invoice Transaksi #${String(lastTransactionId).padStart(4, '0')}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; font-size: 12px; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
                .header h1 { margin: 0; color: #333; font-size: 24px; }
                .header p { margin: 5px 0; color: #666; }
                .invoice-info { display: flex; justify-content: space-between; margin-bottom: 30px; }
                .info-block h3 { margin: 0 0 10px 0; color: #333; font-size: 16px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .text-center { text-align: center; }
                .text-right { text-align: right; }
                .total-row { background-color: #f8f8f8; font-weight: bold; }
                .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #ddd; padding-top: 20px; }
                @media print {
                    body { margin: 0; padding: 15px; }
                    .no-print { display: none !important; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1><i class="fas fa-wrench"></i> Bengkel Motor Jaya</h1>
                <p>Jl. Contoh No. 123, Jakarta | Telp: (021) 1234-5678</p>
                <p>Email: info@bengkelmotorjaya.com</p>
            </div>

            <div class="invoice-info" style="display: flex; justify-content: space-between;">
                <div class="info-block" style="width: 45%;">
                    <h3>Informasi Invoice</h3>
                    <p><strong>No Invoice:</strong> INV-${String(lastTransactionId).padStart(6, '0')}</p>
                    <p><strong>Tanggal:</strong> ${new Date().toLocaleDateString('id-ID')}</p>
                    <p><strong>Waktu:</strong> ${new Date().toLocaleTimeString('id-ID')}</p>
                </div>
                <div class="info-block" style="width: 45%;">
                    <h3>Data Customer</h3>
                    <p><strong>Customer:</strong> ${customer.split(' - ')[0] || 'N/A'}</p>
                    <p><strong>No Kendaraan:</strong> ${noKendaraan}</p>
                    <p><strong>Mekanik:</strong> ${mekanik}</p>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th width="8%">No</th>
                        <th width="45%">Deskripsi</th>
                        <th width="10%">Qty</th>
                        <th width="18%">Harga Satuan</th>
                        <th width="19%">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">1</td>
                        <td>Jasa Service</td>
                        <td class="text-center">1</td>
                        <td class="text-right">Rp ${formatCurrency(hargaJasaNum)}</td>
                        <td class="text-right">Rp ${formatCurrency(hargaJasaNum)}</td>
                    </tr>
                    ${sparepartItems}
                    <tr class="total-row">
                        <td colspan="4" class="text-right"><strong>Subtotal Jasa:</strong></td>
                        <td class="text-right"><strong>Rp ${formatCurrency(hargaJasaNum)}</strong></td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="4" class="text-right"><strong>Subtotal Sparepart:</strong></td>
                        <td class="text-right"><strong>Rp ${formatCurrency(sparepartTotal)}</strong></td>
                    </tr>
                    <tr class="total-row" style="background-color: #e8f5e8;">
                        <td colspan="4" class="text-right"><strong>GRAND TOTAL:</strong></td>
                        <td class="text-right"><strong>Rp ${formatCurrency(grandTotal)}</strong></td>
                    </tr>
                </tbody>
            </table>

            <div class="footer">
                <p>Terima kasih atas kepercayaan Anda menggunakan jasa Bengkel Motor Jaya</p>
                <p>Kepuasan Anda adalah prioritas kami</p>
                <p style="margin-top: 15px;"><em>Invoice ini dicetak pada: ${new Date().toLocaleString('id-ID')}</em></p>
            </div>

            <div class="no-print" style="margin-top: 30px; text-align: center;">
                <button onclick="window.print()" style="background: #007bff; color: white; border: none; padding: 10px 20px; margin: 5px; border-radius: 5px; cursor: pointer;">Print Invoice</button>
                <button onclick="window.close()" style="background: #6c757d; color: white; border: none; padding: 10px 20px; margin: 5px; border-radius: 5px; cursor: pointer;">Close</button>
            </div>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    
    // Auto focus on print window
    printWindow.focus();
    
    showToast('Print preview telah dibuka di tab baru', 'success');
}

// Export Transaction Function
function exportTransaction() {
    // Check if there's transaction data to export
    if (!lastTransactionId) {
        showToast('Belum ada transaksi untuk diekspor. Simpan transaksi terlebih dahulu.', 'warning');
        return;
    }

    try {
        // Prepare transaction data for export
        const customer = document.getElementById('id_customer').selectedOptions[0]?.text || '';
        const noKendaraan = document.getElementById('no_kendaraan').value || '';
        const mekanik = document.getElementById('id_mekanik').selectedOptions[0]?.text || '';
        const hargaJasa = document.getElementById('harga_jasa').value || '0';
        const hargaJasaNum = parseInt(hargaJasa.replace(/[^\d]/g, '')) || 0;
        
        let sparepartTotal = 0;
        sparepartListData.forEach(item => {
            sparepartTotal += item.subtotal;
        });
        
        const grandTotal = hargaJasaNum + sparepartTotal;
        
        // Create CSV content
        let csvContent = "data:text/csv;charset=utf-8,";
        
        // Add header information
        csvContent += "INVOICE BENGKEL MOTOR JAYA\n";
        csvContent += "Jl. Contoh No. 123, Jakarta\n";
        csvContent += "Telp: (021) 1234-5678\n\n";
        
        csvContent += "No Invoice,INV-" + String(lastTransactionId).padStart(6, '0') + "\n";
        csvContent += "Tanggal," + new Date().toLocaleDateString('id-ID') + "\n";
        csvContent += "Waktu," + new Date().toLocaleTimeString('id-ID') + "\n";
        csvContent += "Customer," + customer.split(' - ')[0] + "\n";
        csvContent += "No Kendaraan," + noKendaraan + "\n";
        csvContent += "Mekanik," + mekanik + "\n\n";
        
        // Add items table header
        csvContent += "No,Deskripsi,Qty,Harga Satuan,Subtotal\n";
        
        // Add service item
        csvContent += "1,Jasa Service,1," + hargaJasaNum + "," + hargaJasaNum + "\n";
        
        // Add sparepart items
        let itemNo = 2;
        sparepartListData.forEach(item => {
            csvContent += itemNo + "," + item.nama + "," + item.qty + "," + item.harga + "," + item.subtotal + "\n";
            itemNo++;
        });
        
        // Add totals
        csvContent += "\n";
        csvContent += "Subtotal Jasa,," + hargaJasaNum + "\n";
        csvContent += "Subtotal Sparepart,," + sparepartTotal + "\n";
        csvContent += "GRAND TOTAL,," + grandTotal + "\n";
        
        // Create download link
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `transaksi_${lastTransactionId}_${new Date().toISOString().slice(0,10)}.csv`);
        document.body.appendChild(link);
        
        // Trigger download
        link.click();
        document.body.removeChild(link);
        
        showToast('Data transaksi berhasil diekspor ke file CSV', 'success');
        
    } catch (error) {
        console.error('Export error:', error);
        showToast('Gagal mengekspor data transaksi', 'error');
    }
}
</script>
@endsection