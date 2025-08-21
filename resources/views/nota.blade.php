@extends('layouts.app')

@section('content')

<style>
/* Elemen yang tidak boleh tampil saat print */
@media print {
    .no-print,  /* semua tombol yang diberi class no-print */
    .btn,
    nav, 
     [x-show="open"] {
    display: none !important;
  }    /* semua tombol */
    #btn-kembali, 
    #btn-lunas {
        display: none !important;
        visibility: hidden !important;
    }

}
</style>
<div class="container p-2">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <!-- Header -->
                <div class="card-header bg-white text-black text-center py-2">
                    <h4 class="mb-0"><i class="fas fa-wrench"></i><strong>PT. PGAS SOLUTION</strong> </h4>
                    <small><strong>Jl. Contoh No. 12, Bogor | Telp: (021) 1234-5678</strong></small>
                </div>

                <div class="card-body p-3 bg-white">
                    <!-- Invoice & Customer Info -->
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tr><td><strong>No Invoice:</strong></td><td id="no-invoice">-</td></tr>
                                <tr><td><strong>Tanggal:</strong></td><td id="tanggal">-</td></tr>
                                <tr><td><strong>Waktu:</strong></td><td id="waktu">-</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tr><td><strong>Nama:</strong></td><td id="nama-customer">-</td></tr>
                                <tr><td><strong>Telepon:</strong></td><td id="telepon-customer">-</td></tr>
                                <tr><td><strong>Alamat:</strong></td><td id="alamat-customer">-</td></tr>
                            </table>
                        </div>
                    </div>

                    <!-- Kendaraan Info -->
                    <div class="card-2 text-black mb-4 py-2">
                        <div class="row">
                            <div class="col-md-4"><strong>&nbsp;No Kendaraan:</strong> <span id="no-kendaraan">-</span></div>
                            <div class="col-md-4"><strong>&nbsp;Jenis:</strong> <span id="jenis-kendaraan">-</span></div>
                            <div class="col-md-4"><strong>&nbsp;Mekanik:</strong> <span id="nama-mekanik">-</span></div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="table-responsive mb-2">
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center" style="width:50px">No</th>
                                    <th>Deskripsi</th>
                                    <th class="text-center" style="width:80px">Qty</th>
                                    <th class="text-end" style="width:140px">Harga</th>
                                    <th class="text-end" style="width:160px">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="items-body"></tbody>
                        </table>
                    </div>

                    <!-- Summary & Buttons -->
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="card-2 text-black bg-light py-2 mb-2">
                                <strong>&nbsp;&nbsp;Status Pembayaran:</strong>
                                <span id="status-pembayaran" class="badge bg-danger "><strong>&nbsp;BELUM LUNAS</strong></span>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <button class="btn btn-outline-dark me-2" onclick="printNota()"><i class="fas fa-print"></i> Cetak Invoice</button>
                            <button class="btn btn-outline-secondary me-2" id="btn-kembali"><i class="fas fa-arrow-left"></i> Kembali</button><br>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="card-2 text-black py-2 mb-0">
                        <small id="info-transaksi">&nbsp;-</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Assets minimal --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
/* === KONFIG === */
const token   = "{{ session('token') }}";
const apiBase = "https://apibengkel.up.railway.app/api";

/* === DATA DARI CONTROLLER === */
const transaksi = @json($transaksi);

/* === INIT === */
document.addEventListener('DOMContentLoaded', async () => {
    try{
        renderHeader(transaksi);
        await renderCustomer(transaksi?.id_customer);
        await loadAndRenderDetail(transaksi);
        toggleLunasButton(transaksi?.status_pembayaran);
    }catch(err){
        Swal.fire('Gagal', err?.message || 'Gagal memuat nota', 'error');
    }
});

/* === HEADER/IDENTITAS === */
function renderHeader(tx){
    const idTx = tx?.id_transaksi ?? tx?.id ?? '-';
    const ts   = (tx?.tanggal || tx?.created_at || '').toString();
    const tanggal = ts ? ts.substring(0,10) : new Date().toISOString().substring(0,10);
    const waktu   = ts && ts.length >= 16 ? ts.substring(11,16) : new Date().toTimeString().substring(0,5);

    qs('#no-invoice').textContent = `INV-${String(idTx).padStart(6,'0')}`;
    qs('#tanggal').textContent    = tanggal;
    qs('#waktu').textContent      = waktu;

    qs('#no-kendaraan').textContent   = tx?.no_kendaraan ?? '-';
    qs('#jenis-kendaraan').textContent= tx?.jenis_kendaraan?.jenis_kendaraan ?? '-';
    qs('#nama-mekanik').textContent   = tx?.mekanik?.nama_mekanik ?? '-';

    const st = (tx?.status_pembayaran || '').toLowerCase();
    const badge = qs('#status-pembayaran');
    badge.textContent = (st === 'lunas' ? 'LUNAS' : 'BELUM LUNAS').toUpperCase();
    badge.className = `badge ${st === 'lunas' ? 'bg-success text-black' : 'bg-danger text-black'}`;
}

/* === CUSTOMER === */
async function renderCustomer(idCustomer){
    if(!idCustomer){ return; }

    // Coba endpoint /customers/{id}
    let cust = null;
    try{
        const r = await fetch(`${apiBase}/customers/${idCustomer}`, {headers:{Authorization:`Bearer ${token}`}});
        if(r.ok){
            const j = await r.json();
            cust = j?.data || null;
        }
    }catch(_){}

    // Fallback /customers lalu cari
    if(!cust){
        try{
            const r = await fetch(`${apiBase}/customers`, {headers:{Authorization:`Bearer ${token}`}});
            const j = await r.json();
            const arr = Array.isArray(j?.data) ? j.data : [];
            cust = arr.find(c => String(c.id_customer) === String(idCustomer)) || null;
        }catch(_){}
    }

    qs('#nama-customer').textContent    = cust?.nama_customer ?? '-';
    qs('#telepon-customer').textContent = cust?.telepon ?? '-';
    qs('#alamat-customer').textContent  = cust?.alamat ?? '-';
}

/* === DETAIL (dari tabel detail_transaksi) === */
async function loadAndRenderDetail(tx){
    const tbody = qs('#items-body');
    tbody.innerHTML = '';

    const r = await fetch(`${apiBase}/detail_transaksi`, {headers:{Authorization:`Bearer ${token}`}});
    if(!r.ok) throw new Error('Gagal memuat detail_transaksi');
    const j = await r.json();
    const list = Array.isArray(j?.data) ? j.data : [];

    // Filter menurut id_transaksi ATAU id_customer dari transaksi (hindari $id_customer undefined)
    const rows = list.filter(d =>
        String(d.id_transaksi) === String(tx?.id_transaksi ?? tx?.id) ||
        String(d.id_customer)  === String(tx?.id_customer)
    );

    if(rows.length === 0){
        tbody.innerHTML = `<tr><td colspan="5" class="text-center">&nbsp;&nbsp;Detail transaksi tidak ditemukan</td></tr>`;
        qs('#info-transaksi').textContent = `Transaksi #${tx?.id_transaksi ?? tx?.id ?? '-'} | Data detail tidak tersedia`;
        return;
    }

    // Render baris ringkas (karena tabel detail_transaksi tidak menyimpan nama item per baris)
    let grand = 0;
    rows.forEach((d, idx) => {
        const harga = Number(d.total || 0);
        grand += harga;

        const deskripsi = buildDeskripsi(d); // singkat dari field id_jasa/id_service/id_sparepart
        tbody.insertAdjacentHTML('beforeend', `
            <tr>
                <td class="text-center">${idx+1}</td>
                <td>${deskripsi}</td>
                <td class="text-center">1</td>
                <td class="text-end">Rp ${fmtIDR(harga)}</td>
                <td class="text-end">Rp ${fmtIDR(harga)}</td>
            </tr>
        `);
    });

    const bayar = Number(rows[0]?.bayar || 0);
    const kembalian = Number(rows[0]?.kembalian || Math.max(0, bayar - grand));

    tbody.insertAdjacentHTML('beforeend', `
        <tr class="table-light">
            <td colspan="4" class="text-end"><strong>SUBTOTAL</strong></td>
            <td class="text-end"><strong>Rp ${fmtIDR(grand)}</strong></td>
        </tr>
        <tr class="table-light">
            <td colspan="4" class="text-end"><strong>BAYAR</strong></td>
            <td class="text-end"><strong>Rp ${fmtIDR(bayar)}</strong></td>
        </tr>
        <tr class="table-success">
            <td colspan="4" class="text-end"><strong>KEMBALIAN</strong></td>
            <td class="text-end"><strong>Rp ${fmtIDR(kembalian)}</strong></td>
        </tr>
    `);

    const spkInfo   = rows[0]?.no_spk ?? '-';
    const nopolInfo = rows[0]?.no_kendaraan ?? (tx?.no_kendaraan ?? '-');
    qs('#info-transaksi').textContent = `Total Detail: ${rows.length} | SPK: ${spkInfo} | No Kendaraan: ${nopolInfo}`;
}

/* === AKSI === */
document.getElementById('btn-kembali').addEventListener('click', () => {
    if (document.referrer && document.referrer !== location.href) {
        history.back();
    } else {
        window.location.href = "{{ url()->previous() ?? route('dashboard') }}";
    }
});

function printNota(){ window.print(); }

/* === UTIL === */
function qs(sel){ return document.querySelector(sel); }
function fmtIDR(n){ return new Intl.NumberFormat('id-ID').format(Number(n)||0); }
function buildDeskripsi(d){
    const parts = [];
    if(d.id_jasa)      parts.push(`Jasa #${d.id_jasa}`);
    if(d.id_service)   parts.push(`Service #${d.id_service}`);
    if(d.id_sparepart) parts.push(`Sparepart #${d.id_sparepart}`);
    return parts.join(' / ') || 'Detail Transaksi';
}
function toggleLunasButton(status){
    const st = (status || '').toLowerCase();
    if(st === 'lunas'){ qs('#btn-lunas')?.classList.add('d-none'); }
}
</script>
@endsection
