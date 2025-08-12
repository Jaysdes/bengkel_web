@extends('layouts.app')

@section('content')
<div class="container mt-4" id="nota">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header Bengkel -->
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-gradient-primary text-white text-center py-3">
                    <h3 class="mb-1"><i class="fas fa-wrench"></i> Bengkel Motor Jaya</h3>
                    <p class="mb-0">Jl. Contoh No. 123, Jakarta | Telp: (021) 1234-5678</p>
                </div>

                <div class="card-body p-4">
                    <!-- Invoice Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3"><i class="fas fa-file-invoice"></i> INVOICE</h5>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td><strong>No Invoice:</strong></td>
                                    <td>INV-{{ str_pad($transaksi['id_transaksi'], 6, '0', STR_PAD_LEFT) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal:</strong></td>
                                    <td>{{ $transaksi['tanggal'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Waktu:</strong></td>
                                    <td>{{ $transaksi['waktu'] }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3"><i class="fas fa-user"></i> Data Customer</h5>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td><strong>Nama:</strong></td>
                                    <td>{{ $transaksi['customer']['nama_customer'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Telepon:</strong></td>
                                    <td>{{ $transaksi['customer']['telepon'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Alamat:</strong></td>
                                    <td>{{ $transaksi['customer']['alamat'] ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Vehicle Info -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6 class="mb-2"><i class="fas fa-car"></i> Informasi Kendaraan</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>No Kendaraan:</strong> {{ $transaksi['no_kendaraan'] }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Jenis:</strong> {{ $transaksi['jenis_kendaraan']['jenis_kendaraan'] ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Mekanik:</strong> {{ $transaksi['mekanik']['nama_mekanik'] ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="45%">Deskripsi</th>
                                    <th width="10%">Qty</th>
                                    <th width="20%">Harga Satuan</th>
                                    <th width="20%">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach($transaksi['items'] as $item)
                                <tr>
                                    <td class="text-center">{{ $no++ }}</td>
                                    <td>
                                        {{ $item['nama'] }}
                                        @if($item['type'] == 'jasa')
                                            <small class="text-muted d-block">
                                                <i class="fas fa-tools"></i> Jasa Service
                                            </small>
                                        @else
                                            <small class="text-muted d-block">
                                                <i class="fas fa-cog"></i> Sparepart
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item['qty'] }}</td>
                                    <td class="text-end">Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                                
                                <!-- Summary Rows -->
                                <tr class="table-light">
                                    <td colspan="4" class="text-end"><strong>Subtotal Jasa:</strong></td>
                                    <td class="text-end"><strong>Rp {{ number_format($transaksi['subtotal_jasa'], 0, ',', '.') }}</strong></td>
                                </tr>
                                <tr class="table-light">
                                    <td colspan="4" class="text-end"><strong>Subtotal Sparepart:</strong></td>
                                    <td class="text-end"><strong>Rp {{ number_format($transaksi['subtotal_sparepart'], 0, ',', '.') }}</strong></td>
                                </tr>
                                <tr class="table-success">
                                    <td colspan="4" class="text-end"><strong>GRAND TOTAL:</strong></td>
                                    <td class="text-end"><h5 class="mb-0"><strong>Rp {{ number_format($transaksi['grand_total'], 0, ',', '.') }}</strong></h5></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Payment Status -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="alert alert-warning">
                                <h6 class="mb-2"><i class="fas fa-credit-card"></i> Status Pembayaran</h6>
                                @if(($transaksi['status_pembayaran'] ?? 'belum_lunas') === 'lunas')
                                    <span class="badge bg-success fs-6">
                                        <i class="fas fa-check-circle"></i> LUNAS
                                    </span>
                                @else
                                    <span class="badge bg-danger fs-6">
                                        <i class="fas fa-exclamation-circle"></i> BELUM LUNAS
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-secondary">
                                <h6 class="mb-2"><i class="fas fa-info-circle"></i> Informasi</h6>
                                <small>
                                    Total Item: {{ $transaksi['total_items'] ?? 0 }}<br>
                                    SPK: {{ $transaksi['spk']['id_spk'] ?? 'N/A' }}<br>
                                    Keluhan: {{ $transaksi['spk']['keluhan'] ?? 'N/A' }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="text-center">
                        <button class="btn btn-primary me-2" onclick="window.print()">
                            <i class="fas fa-print"></i> Cetak Invoice
                        </button>
                        <a href="{{ route('transaksi') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        @if(($transaksi['status_pembayaran'] ?? 'belum_lunas') !== 'lunas')
                        <button class="btn btn-success ms-2" onclick="markAsPaid({{ $transaksi['id_transaksi'] }})">
                            <i class="fas fa-money-bill-wave"></i> Tandai Lunas
                        </button>
                        @endif
                    </div>

                    <!-- Footer -->
                    <div class="mt-4 pt-3 border-top text-center">
                        <small class="text-muted">
                            Terima kasih atas kepercayaan Anda menggunakan jasa Bengkel Motor Jaya<br>
                            <i class="fas fa-heart text-danger"></i> Kepuasan Anda adalah prioritas kami
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .navbar, .sidebar { display: none !important; }
    .container { max-width: 100% !important; }
    .card { border: none !important; box-shadow: none !important; }
    body { font-size: 12px; }
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.neon-shadow {
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
    transition: all 0.3s ease;
}

.neon-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
}
</style>

<script>
const API_URL = '{{ env('API_URL', 'http://localhost:8001/api') }}';

function markAsPaid(transaksiId) {
    if (confirm('Apakah Anda yakin ingin menandai transaksi ini sebagai lunas?')) {
        fetch(`${API_URL}/transaksi/${transaksiId}/mark-paid`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            showToast('Transaksi berhasil ditandai sebagai lunas!', 'success');
            // Reload the page to show updated status
            setTimeout(() => {
                location.reload();
            }, 1500);
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Gagal menandai transaksi sebagai lunas. Silakan coba lagi.', 'error');
        });
    }
}

function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}
</script>
@endsection