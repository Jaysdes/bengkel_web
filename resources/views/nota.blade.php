<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice Bengkel - PT. PGAS SOLUTION</title>

<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Bootstrap CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<style>
:root {
    --neon-blue: #3b82f6;
    --neon-blue-light: #60a5fa;
    --dark-card: #1f2937;
    --dark-border: #1d4ed8;
}
body { font-family: 'Inter', sans-serif; background: #f3f4f6; color: #000; }
.neon-glow { box-shadow: 0 0 10px var(--neon-blue); }
.btn-neon { background: linear-gradient(45deg,#0b0b0b,#151515); border: 2px solid var(--neon-blue); color: #fff; padding: 0.4rem 0.8rem; border-radius: 6px; font-weight: 600; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; }
.btn-neon:hover { background: var(--neon-blue); color: #0b0b0b; box-shadow: 0 0 15px var(--neon-blue); transform: translateY(-1px); }
.table-neon { background: #1f2937; border-radius: 8px; overflow: hidden; border: 1px solid var(--dark-border); color: #fff; font-size: 0.75rem; }
.table-neon th, .table-neon td { border-color: rgba(255,255,255,0.1); padding: 0.35rem 0.5rem; }
.alert-neon { border-radius: 8px; padding: 0.5rem; border: 1px solid var(--neon-blue); background: rgba(59,130,246,0.05); color: var(--neon-blue); font-size: 0.8rem; }
.card { border-radius: 10px; }
@media print {
    body { margin: 0; padding: 0; }
    .no-print { display: none !important; }
    .container { max-width: 210mm; min-height: 297mm; padding: 10mm; }
    .card, .table-neon { box-shadow: none !important; border: 1px solid #000 !important; background: #fff !important; color: #000 !important; }
}
</style>
</head>
<body class="p-2">

<div class="container mx-auto" id="nota">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <!-- Header -->
                <div class="card-header bg-gradient-to-r from-blue-500 to-blue-600 text-white text-center py-2 no-print">
                    <h4 class="mb-0"><i class="fas fa-wrench"></i> PT. PGAS SOLUTION</h4>
                    <small>Jl. Contoh No. 12, Bogor | Telp: (021) 1234-5678</small>
                </div>

                <div class="card-body p-3 bg-white">
                    <!-- Invoice & Customer Info -->
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr><td><strong>No Invoice:</strong></td><td id="no-invoice">INV-000001</td></tr>
                                <tr><td><strong>Tanggal:</strong></td><td id="tanggal">2025-08-13</td></tr>
                                <tr><td><strong>Waktu:</strong></td><td id="waktu">10:30</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr><td><strong>Nama:</strong></td><td id="nama-customer">Rexjy</td></tr>
                                <tr><td><strong>Telepon:</strong></td><td id="telepon-customer">08123456789</td></tr>
                                <tr><td><strong>Alamat:</strong></td><td id="alamat-customer">Jl. Contoh No. 12</td></tr>
                            </table>
                        </div>
                    </div>

                    <!-- Kendaraan Info -->
                    <div class="alert alert-neon mb-2">
                        <div class="row">
                            <div class="col-md-4"><strong>No Kendaraan:</strong> <span id="no-kendaraan">B1234XYZ</span></div>
                            <div class="col-md-4"><strong>Jenis:</strong> <span id="jenis-kendaraan">Motor</span></div>
                            <div class="col-md-4"><strong>Mekanik:</strong> <span id="nama-mekanik">Andi</span></div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="table-responsive mb-2">
                        <table class="table table-neon">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Deskripsi</th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="items-body"></tbody>
                        </table>
                    </div>

                    <!-- Summary & Buttons -->
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="alert alert-neon">
                                <strong>Status Pembayaran:</strong> <span id="status-pembayaran" class="badge bg-success">LUNAS</span>
                            </div>
                        </div>
                        <div class="col-md-6 text-end no-print">
                        <button class="btn btn-neon me-2" onclick="printNota()"><i class="fas fa-print"></i> Cetak Invoice</button>
                        <button class="btn btn-neon me-1" id="btn-kembali">
    <i class="fas fa-arrow-left"></i> Kembali
</button>
                            <button id="btn-lunas" class="btn btn-neon" onclick="markAsPaid()"><i class="fas fa-money-bill-wave"></i> Tandai Lunas</button>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="alert alert-neon mb-0">
                        <small id="info-transaksi">Total Item: 3 | SPK: 001 | Keluhan: Mesin panas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const transaksi = {
    id_transaksi: 1,
    tanggal: "2025-08-13",
    waktu: "10:30",
    customer: { nama_customer: "Rexjy", telepon: "08123456789", alamat: "Jl. Contoh No. 12" },
    no_kendaraan: "B1234XYZ",
    jenis_kendaraan: { jenis_kendaraan: "Motor" },
    mekanik: { nama_mekanik: "Andi" },
    items: [
        { nama: "Ganti Oli", type: "jasa", qty: 1, harga: 50000, subtotal: 50000 },
        { nama: "Filter Oli", type: "sparepart", qty: 1, harga: 25000, subtotal: 25000 },
        { nama: "Tune Up", type: "jasa", qty: 1, harga: 75000, subtotal: 75000 }
    ],
    subtotal_jasa: 125000,
    subtotal_sparepart: 25000,
    grand_total: 150000,
    status_pembayaran: "belum_lunas",
    total_items: 3,
    spk: { id_spk: "001", keluhan: "Mesin panas" }
};
document.getElementById('btn-kembali').addEventListener('click', function() {
    window.location.href = '{{ route("dashboard") }}';
});
// Render items
const tbody = document.getElementById("items-body");
transaksi.items.forEach((item, i) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
        <td class="text-center">${i+1}</td>
        <td>${item.nama} <small class="text-muted d-block">${item.type=="jasa" ? "Jasa Service" : "Sparepart"}</small></td>
        <td class="text-center">${item.qty}</td>
        <td class="text-end">Rp ${item.harga.toLocaleString('id-ID')}</td>
        <td class="text-end">Rp ${item.subtotal.toLocaleString('id-ID')}</td>
    `;
    tbody.appendChild(tr);
});
tbody.innerHTML += `
<tr class="table-light"><td colspan="4" class="text-end"><strong>Subtotal Jasa:</strong></td><td class="text-end"><strong>Rp ${transaksi.subtotal_jasa.toLocaleString('id-ID')}</strong></td></tr>
<tr class="table-light"><td colspan="4" class="text-end"><strong>Subtotal Sparepart:</strong></td><td class="text-end"><strong>Rp ${transaksi.subtotal_sparepart.toLocaleString('id-ID')}</strong></td></tr>
<tr class="table-success"><td colspan="4" class="text-end"><strong>GRAND TOTAL:</strong></td><td class="text-end"><strong>Rp ${transaksi.grand_total.toLocaleString('id-ID')}</strong></td></tr>
`;

// Render invoice info
document.getElementById("no-invoice").innerText = `INV-${String(transaksi.id_transaksi).padStart(6,'0')}`;
document.getElementById("tanggal").innerText = transaksi.tanggal;
document.getElementById("waktu").innerText = transaksi.waktu;
document.getElementById("nama-customer").innerText = transaksi.customer.nama_customer;
document.getElementById("telepon-customer").innerText = transaksi.customer.telepon;
document.getElementById("alamat-customer").innerText = transaksi.customer.alamat;
document.getElementById("no-kendaraan").innerText = transaksi.no_kendaraan;
document.getElementById("jenis-kendaraan").innerText = transaksi.jenis_kendaraan.jenis_kendaraan;
document.getElementById("nama-mekanik").innerText = transaksi.mekanik.nama_mekanik;
document.getElementById("status-pembayaran").innerText = transaksi.status_pembayaran === 'lunas' ? 'LUNAS' : 'BELUM LUNAS';
document.getElementById("info-transaksi").innerText = `Total Item: ${transaksi.total_items} | SPK: ${transaksi.spk.id_spk} | Keluhan: ${transaksi.spk.keluhan}`;

// Handle tombol lunas
const btnLunas = document.getElementById("btn-lunas");
if(transaksi.status_pembayaran === "lunas") btnLunas.style.display = "none";

function markAsPaid(){
    transaksi.status_pembayaran = "lunas";
    document.getElementById("status-pembayaran").innerText = "LUNAS";
    btnLunas.style.display = "none";
    alert("Transaksi berhasil ditandai LUNAS!");
}

function printNota(){ window.print(); }
</script>

</body>
</html>
