@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0 text-dark fw-bold">
                        <i class="fas fa-cash-register"></i> Form Transaksi Bengkel
                    </h4>
                    <p class="text-muted mb-0">Kelola transaksi service kendaraan</p>
                </div>
                <div>
                    <a href="{{ url('/daftar-transaksi') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i> Lihat Daftar Transaksi
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <div class="row">
        <div class="col-xl-8">
            <form id="dataForm" class="needs-validation" novalidate>
                @csrf
                
                <!-- SPK Selection -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Pilih SPK (Surat Perintah Kerja)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <label class="form-label">SPK <span class="text-danger">*</span></label>
                                <select id="id_spk" class="form-select form-select-lg" required>
                                    <option value="">-- Pilih SPK --</option>
                                </select>
                                <div class="invalid-feedback">Silakan pilih SPK</div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-info w-100" onclick="refreshSPK()">
                                    <i class="fas fa-sync-alt"></i> Refresh SPK
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer & Vehicle Info -->
                <div class="card border-0 shadow-sm mb-4" id="customerInfo" style="display: none;">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-user-car"></i> Informasi Customer & Kendaraan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Customer</label>
                                <select id="id_customer" class="form-select" disabled>
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">No Kendaraan</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-car"></i></span>
                                    <input type="text" id="no_kendaraan" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">No Telepon Customer</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text" id="telp_customer" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mechanic Selection -->
                <div class="card border-0 shadow-sm mb-4" id="mechanicInfo" style="display: none;">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-user-cog"></i> Pilih Mekanik</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Mekanik <span class="text-danger">*</span></label>
                                <select id="id_mekanik" class="form-select" required>
                                    <option value="">-- Pilih Mekanik --</option>
                                </select>
                                <div class="invalid-feedback">Silakan pilih mekanik</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No Telepon Mekanik</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                    <input type="text" id="telp_mekanik" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Info -->
                <div class="card border-0 shadow-sm mb-4" id="serviceInfo" style="display: none;">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-tools"></i> Informasi Jasa Service</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Jenis Jasa</label>
                                <select id="id_jasa" class="form-select" disabled>
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Harga Jasa</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" id="harga_jasa" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Jenis Service</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-check-inline p-3 border rounded">
                                            <input class="form-check-input" type="radio" name="jenis_service" id="berkala" value="1" disabled>
                                            <label class="form-check-label ms-2" for="berkala">
                                                <i class="fas fa-calendar-check text-success"></i> Service Berkala
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-check-inline p-3 border rounded">
                                            <input class="form-check-input" type="radio" name="jenis_service" id="tidak_berkala" value="2" disabled>
                                            <label class="form-check-label ms-2" for="tidak_berkala">
                                                <i class="fas fa-wrench text-warning"></i> Service Tidak Berkala
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sparepart Section -->
                <div class="card border-0 shadow-sm mb-4" id="sparepartSection" style="display: none;">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-cogs"></i> Tambah Sparepart</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Pilih Sparepart</label>
                                <select id="id_sparepart" class="form-select">
                                    <option value="">-- Pilih Sparepart --</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Qty</label>
                                <input type="number" id="qty_sparepart" class="form-control" min="1" value="1">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-success w-100" onclick="addSparepart()">
                                    <i class="fas fa-plus"></i> Tambah
                                </button>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-secondary w-100" onclick="clearSpareparts()">
                                    <i class="fas fa-trash"></i> Clear
                                </button>
                            </div>
                        </div>

                        <!-- Sparepart List Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="40%">Nama Sparepart</th>
                                        <th width="15%">Qty</th>
                                        <th width="20%">Harga Satuan</th>
                                        <th width="20%">Subtotal</th>
                                        <th width="5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="sparepartList">
                                    <tr id="noSparepartRow">
                                        <td colspan="5" class="text-center text-muted py-3">
                                            <i class="fas fa-info-circle"></i> Belum ada sparepart yang ditambahkan
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card border-0 shadow-sm" id="submitSection" style="display: none;">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-primary btn-lg me-3" id="submitBtn">
                            <i class="fas fa-save"></i> Simpan Transaksi
                        </button>
                        <button type="button" class="btn btn-secondary btn-lg" onclick="resetForm()">
                            <i class="fas fa-undo"></i> Reset Form
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Summary Sidebar -->
        <div class="col-xl-4">
            <div class="sticky-top" style="top: 20px;">
                <!-- Summary Card -->
                <div class="card border-0 shadow-lg" id="summaryCard" style="display: none;">
                    <div class="card-header bg-gradient-success text-white">
                        <h5 class="mb-0"><i class="fas fa-calculator"></i> Ringkasan Transaksi</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Biaya Jasa:</span>
                            <strong id="summary-jasa">Rp 0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Biaya Sparepart:</span>
                            <strong id="summary-sparepart">Rp 0</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="h5">Total:</span>
                            <span class="h4 text-success" id="summary-total">Rp 0</span>
                        </div>
                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Total akan otomatis terupdate
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Progress Card -->
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-tasks"></i> Progress Pengisian</h6>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-success" id="progressBar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <small class="text-muted">
                            <span id="progressText">Pilih SPK untuk memulai</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mb-0">Menyimpan transaksi...</p>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-body text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                </div>
                <h4 class="text-success mb-3">Transaksi Berhasil!</h4>
                <p class="text-muted mb-4">Transaksi telah berhasil disimpan</p>
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <button type="button" class="btn btn-primary" onclick="createNewTransaction()">
                        <i class="fas fa-plus"></i> Transaksi Baru
                    </button>
                    <button type="button" class="btn btn-success" id="viewInvoiceBtn">
                        <i class="fas fa-receipt"></i> Lihat Nota
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
}

.card {
    transition: all 0.3s ease;
    border-radius: 15px !important;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.btn {
    border-radius: 25px !important;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.form-control, .form-select {
    border-radius: 10px !important;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 10px rgba(0,123,255,0.1);
}

.sticky-top {
    transition: all 0.3s ease;
}

.progress-bar {
    transition: width 0.5s ease-in-out;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
    transform: scale(1.01);
    transition: all 0.2s ease;
}

.animate-slide-down {
    animation: slideDown 0.5s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-check {
    transition: all 0.3s ease;
}

.form-check:hover {
    background-color: rgba(0,123,255,0.05);
}
</style>

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
        showToast('Gagal memuat data formulir', 'danger');
        console.error('Error:', error);
    });
}

function setupEventListeners() {
    document.getElementById('id_spk').addEventListener('change', autofillFromSPK);
    document.getElementById('id_jasa').addEventListener('change', calculateTotal);
    document.getElementById('id_mekanik').addEventListener('change', fetchMekanikPhone);
    document.getElementById('dataForm').addEventListener('submit', handleSubmit);
    document.getElementById('id_sparepart').addEventListener('change', updateSparepartHint);
}

function loadDropdown(endpoint, elementId, includeHarga = false) {
    return fetch(`${API_URL}/${endpoint}`)
        .then(res => res.json())
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
        });
}

function loadSPKDropdown() {
    return fetch(`${API_URL}/spk`)
        .then(res => res.json())
        .then(result => {
            const data = result.data || result;
            const select = document.getElementById('id_spk');
            select.innerHTML = '<option value="">-- Pilih SPK --</option>';
            data.forEach(spk => {
                select.innerHTML += `<option value="${spk.id_spk}">SPK #${String(spk.id_spk).padStart(4, '0')} - ${spk.keluhan || 'No Description'}</option>`;
            });
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
        .then(res => res.json())
        .then(result => {
            const spk = result.data || result;
            document.getElementById('id_customer').value = spk.id_customer;
            document.getElementById('id_jasa').value = spk.id_jasa;
            document.getElementById('no_kendaraan').value = spk.no_kendaraan;

            // Show sections with animation
            showSection('customerInfo');
            showSection('mechanicInfo');
            showSection('serviceInfo');
            showSection('sparepartSection');
            showSection('submitSection');
            showSection('summaryCard');

            // Load customer phone
            return fetch(`${API_URL}/customers/${spk.id_customer}`);
        })
        .then(res => res.json())
        .then(customer => {
            document.getElementById('telp_customer').value = customer.data.telepon;

            // Load service price
            const jasaSelect = document.getElementById('id_jasa');
            const harga = parseInt(jasaSelect.selectedOptions[0]?.getAttribute('data-harga')) || 0;
            document.getElementById('harga_jasa').value = formatCurrency(harga);
            
            calculateTotal();
            updateProgress(60);
            showToast('Data SPK berhasil dimuat', 'success');
        })
        .catch(error => {
            showToast('Gagal memuat data SPK', 'danger');
            console.error('Error:', error);
        });
}

function showSection(sectionId) {
    const section = document.getElementById(sectionId);
    section.style.display = 'block';
    section.classList.add('animate-slide-down');
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
        .then(res => res.json())
        .then(result => {
            document.getElementById('telp_mekanik').value = result.data.telepon;
            updateProgress(90);
            updateProgressText('Siap untuk submit transaksi');
        });
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
        showToast(`Qty melebihi stok yang tersedia (${stok})`, 'danger');
        return;
    }

    // Check if sparepart already exists
    const existingIndex = sparepartListData.findIndex(item => item.id_sparepart === parseInt(id));
    if (existingIndex !== -1) {
        const newQty = sparepartListData[existingIndex].qty + qty;
        if (newQty > stok) {
            showToast(`Total qty akan melebihi stok yang tersedia (${stok})`, 'danger');
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
    
    showToast('Sparepart berhasil ditambahkan', 'success');
}

function renderSparepartTable() {
    const tbody = document.getElementById('sparepartList');
    const noDataRow = document.getElementById('noSparepartRow');

    if (sparepartListData.length === 0) {
        noDataRow.style.display = 'table-row';
        return;
    }

    noDataRow.style.display = 'none';
    
    // Clear existing rows except no-data row
    Array.from(tbody.children).forEach(row => {
        if (row.id !== 'noSparepartRow') {
            row.remove();
        }
    });

    sparepartListData.forEach((item, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <div class="d-flex align-items-center">
                    <i class="fas fa-cog text-secondary me-2"></i>
                    ${item.nama}
                </div>
            </td>
            <td class="text-center">
                <span class="badge bg-primary">${item.qty}</span>
            </td>
            <td class="text-end">Rp ${formatCurrency(item.harga)}</td>
            <td class="text-end"><strong>Rp ${formatCurrency(item.subtotal)}</strong></td>
            <td class="text-center">
                <button class="btn btn-sm btn-outline-danger" onclick="removeSparepartByIndex(${index})" title="Hapus item">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function removeSparepartByIndex(index) {
    sparepartListData.splice(index, 1);
    renderSparepartTable();
    calculateTotal();
    showToast('Sparepart dihapus', 'info');
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

function handleSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    if (!form.checkValidity()) {
        e.stopPropagation();
        form.classList.add('was-validated');
        showToast('Mohon lengkapi semua field yang wajib', 'danger');
        return;
    }

    if (sparepartListData.length === 0) {
        if (!confirm('Tidak ada sparepart yang ditambahkan. Lanjutkan dengan hanya jasa saja?')) {
            return;
        }
    }

    // Show loading modal
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    loadingModal.show();

    const hargaJasaText = document.getElementById('harga_jasa').value.replace(/[^\d]/g, '');
    const hargaSparepart = sparepartListData.reduce((sum, item) => sum + item.subtotal, 0);

    const data = {
        id_spk: parseInt(document.getElementById('id_spk').value),
        id_customer: parseInt(document.getElementById('id_customer').value),
        id_jenis: parseInt(document.getElementById('id_jasa').value),
        no_kendaraan: document.getElementById('no_kendaraan').value,
        telepon: document.getElementById('telp_customer').value,
        id_mekanik: parseInt(document.getElementById('id_mekanik').value),
        harga_jasa: parseInt(hargaJasaText) || 0,
        harga_sparepart: hargaSparepart,
        total: parseInt(hargaJasaText) + hargaSparepart
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

        // Create process record
        const prosesData = {
            id_transaksi: transaksi.id_transaksi,
            id_mekanik: data.id_mekanik,
            status: "dalam_antrian",
            keterangan: "Transaksi baru dibuat dan menunggu proses",
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
        
        if (!res.ok) {
            showToast('Transaksi berhasil, namun gagal membuat proses', 'warning');
        }

        // Show success modal
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
        
        // Setup view invoice button
        document.getElementById('viewInvoiceBtn').onclick = function() {
            window.location.href = `/nota/${lastTransactionId}`;
        };

        updateProgress(100);
        updateProgressText('Transaksi berhasil disimpan!');
    })
    .catch(err => {
        loadingModal.hide();
        console.error('Error:', err);
        showToast('Gagal menyimpan transaksi: ' + err.message, 'danger');
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
}

function refreshSPK() {
    showToast('Memuat ulang data SPK...', 'info');
    loadSPKDropdown().then(() => {
        showToast('Data SPK berhasil dimuat ulang', 'success');
    });
}

function updateProgress(percentage) {
    formProgress = percentage;
    const progressBar = document.getElementById('progressBar');
    progressBar.style.width = percentage + '%';
    
    if (percentage <= 25) {
        progressBar.className = 'progress-bar bg-danger';
    } else if (percentage <= 50) {
        progressBar.className = 'progress-bar bg-warning';
    } else if (percentage <= 75) {
        progressBar.className = 'progress-bar bg-info';
    } else {
        progressBar.className = 'progress-bar bg-success';
    }
}

function updateProgressText(text) {
    document.getElementById('progressText').textContent = text;
}

function updateSparepartHint() {
    const select = document.getElementById('id_sparepart');
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        const stok = selectedOption.getAttribute('data-stok');
        const harga = selectedOption.getAttribute('data-harga');
        showToast(`Stok: ${stok}, Harga: Rp ${formatCurrency(harga)}`, 'info');
    }
}

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID').format(amount || 0);
}

function showToast(message, type = 'info') {
    // Create toast container if doesn't exist
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-info-circle me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    container.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}
</script>
@endsection