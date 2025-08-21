@extends('layouts.app')
@section('content')
@php
    $role = session('user')['role'] ?? '';
@endphp

<h4 class="mb-4 text-xl font-bold">Panel Teknisi</h4>

<div class="card p-3 mb-4">
    <div class="d-flex align-items-center gap-2 mb-3">
        <input id="searchInputTeknisi" class="form-control" placeholder="Cari SPK (service, customer, no kendaraan, status)">
        <select id="filterStatus" class="form-select" style="max-width:220px">
            <option value="">Semua Status</option>
            <option value="di proses mekanik">di proses mekanik</option>
            <option value="sudah di proses">sudah di proses</option>
            <option value="selesai">selesai di proses</option>
        </select>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Service</th>
                    <th>Jasa</th>
                    <th>Customer</th>
                    <th>Jenis</th>
                    <th>No Kendaraan</th>
                    <th>Keluhan</th>
                    <th>Catatan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tbTeknisi"></tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <div id="infoTeknisi" class="text-muted small"></div>
        <div class="d-flex gap-2">
            <button class="btn btn-light btn-sm" onclick="prevPageT()">Prev</button>
            <div id="pagesTeknisi" class="d-inline-block"></div>
            <button class="btn btn-light btn-sm" onclick="nextPageT()">Next</button>
        </div>
    </div>
</div>

<!-- Modal Kerjakan -->
<div class="modal fade" id="kerjakanModal" tabindex="-1" aria-labelledby="kerjakanLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content text-dark bg-white">
      <div class="modal-header">
        <h5 class="modal-title" id="kerjakanLabel">Kerjakan SPK</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <form id="formKerjakan">
            <input type="hidden" id="k_id_spk">

            <div class="mb-2">
                <label class="form-label">Tanggal</label>
                <input id="k_tanggal" class="form-control" type="date" >
            </div>

            <div class="mb-2">
                <label class="form-label">Customer</label>
                <input id="k_customer" class="form-control" type="text" >
            </div>

            <div class="mb-2">
                <label class="form-label">No Kendaraan</label>
                <input id="k_nopol" class="form-control" type="text" >
            </div>

            <div class="mb-2">
                <label class="form-label">Keluhan</label>
                <textarea id="k_keluhan" class="form-control" rows="2" ></textarea>
            </div>

            <div class="mb-2">
                <label class="form-label">Pilih Jasa</label>
                <select id="k_id_jasa" class="form-select" required></select>
            </div>


            <div class="mb-2">
                <label class="form-label">Catatan</label>
                <textarea id="k_catatan" class="form-control" rows="3" placeholder="Catatan teknisi (opsional)"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <div id="k_status_label" class="mb-2"></div>
                <input type="hidden" id="k_status" value="sudah di proses">
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-success" id="btnSimpanSelesai">Tandai Selesai</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanProses">Simpan</button>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content text-dark bg-white">
      <div class="modal-header">
        <h5 class="modal-title" id="detailLabel">Detail Penyelesaian SPK</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <div id="detailContent" class="row g-3"></div>
      </div>
    </div>
  </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
/** ====== KONFIG ====== **/
const token = "{{ session('token') }}";
const apiBase = 'https://apibengkel.up.railway.app/api';

/** ====== REF MAPS ====== **/
const jasaMapT = {};
const serviceMapT = {};
const customerMapT = {};

/** ====== STATE ====== **/
let allSpkT = [];
let filteredT = [];
let pageT = 1;
let perPageT = 10;

/** ====== ELEM ====== **/
const tbTeknisi = document.getElementById('tbTeknisi');
const pagesTeknisi = document.getElementById('pagesTeknisi');
const infoTeknisi = document.getElementById('infoTeknisi');
const searchTek = document.getElementById('searchInputTeknisi');
const filterStatus = document.getElementById('filterStatus');

/** ====== UTIL ====== **/
function fmtDate(d) {
    if (!d) return '-';
    const date = new Date(d);
    if (isNaN(date.getTime())) return '-';
    return new Intl.DateTimeFormat('id-ID', { year:'numeric', month:'2-digit', day:'2-digit' }).format(date);
}
function lower(v){ return (v ?? '').toString().toLowerCase(); }
function badge(status){
    const s = lower(status);
    if (s.includes('selesai')) return `<span class="badge bg-success">selesai</span>`;
    if (s.includes('sudah')) return `<span class="badge bg-primary">sudah di proses</span>`;
    return `<span class="badge bg-warning text-dark">di proses mekanik</span>`;
}
function toastOK(title){
    Swal.fire({toast:true,position:'top-end',icon:'success',title,showConfirmButton:false,timer:1800});
}
function toastWarn(title){
    Swal.fire({toast:true,position:'top-end',icon:'warning',title,showConfirmButton:false,timer:2000});
}
function toastErr(title){
    Swal.fire({toast:true,position:'top-end',icon:'error',title,showConfirmButton:false,timer:2200});
}

/** ====== API ====== **/
async function apiGet(url){
    const r = await fetch(url,{headers:{Authorization:`Bearer ${token}`}});
    const j = await r.json().catch(()=>({}));
    if(!r.ok) throw new Error(j?.message||`HTTP ${r.status}`);
    return j;
}
async function apiSend(url,method,body){
    const r = await fetch(url,{
        method,
        headers:{'Content-Type':'application/json', Authorization:`Bearer ${token}`},
        body: JSON.stringify(body)
    });
    const j = await r.json().catch(()=>({}));
    if(!r.ok) throw new Error(j?.message||j?.error||`HTTP ${r.status}`);
    return j;
}

/** ====== LOAD REFS ====== **/
async function loadRefs(){
    const [jasa, svc, cust] = await Promise.all([
        apiGet(`${apiBase}/jenis_jasa`).catch(()=>({data:[] })),
        apiGet(`${apiBase}/jenis_service`).catch(()=>({data:[] })),
        apiGet(`${apiBase}/customers`).catch(()=>({data:[] })),
    ]);

    (jasa.data||[]).forEach(x=> jasaMapT[x.id_jasa]=x.nama_jasa);
    (svc.data||[]).forEach(x=> serviceMapT[x.id_service]=x.jenis_service);
    (cust.data||[]).forEach(x=> customerMapT[x.id_customer]=x.nama_customer);

    // isi dropdown jasa di modal
    const sel = document.getElementById('k_id_jasa');
    sel.innerHTML = `<option value="">-- Pilih Jasa --</option>`;
    (jasa.data||[]).forEach(x=>{
        sel.innerHTML += `<option value="${x.id_jasa}">${x.nama_jasa}</option>`;
    });
}

/** ====== LOAD SPK ====== **/
async function loadSpkT(){
    const j = await apiGet(`${apiBase}/spk`);
    allSpkT = Array.isArray(j?.data) ? j.data : [];
    allSpkT.sort((a,b)=> (b.id_spk??0)-(a.id_spk??0));
    filterAndRenderT();
}

/** ====== FILTER + RENDER ====== **/
function filterAndRenderT(){ 
    const q = lower(searchTek?.value || '');
    const f = lower(filterStatus?.value || '');

    filteredT = allSpkT.filter(s=>{
        const jasa = lower(jasaMapT[s.id_jasa]);
        const cust = lower(customerMapT[s.id_customer]);
        const svc  = lower(serviceMapT[s.id_service]);
        const no   = lower(s.no_kendaraan);
        const kel  = lower(s.keluhan);
        const cat  = lower(s.catatan);
        const st   = lower(s.status);

        const inText = [jasa,cust,svc,no,kel,cat,st,fmtDate(s.tanggal_spk)].some(x=> (x||'').includes(q));
        const statusOk = !f || st === f;
        return inText && statusOk;
    });

    const total = filteredT.length;
    const totalPages = Math.ceil(total / perPageT) || 1;
    if (pageT > totalPages) pageT = totalPages;

    const start = (pageT-1)*perPageT;
    const data = filteredT.slice(start, start+perPageT);

    tbTeknisi.innerHTML = '';
    data.forEach((spk,i)=>{
        const jenis = spk.id_jenis==1?'Motor':(spk.id_jenis==2?'Mobil':'-');
        const svc = serviceMapT[spk.id_service] ?? '-';
        const jasa = spk.id_jasa ? (jasaMapT[spk.id_jasa] ?? spk.id_jasa) : '-';
        const cust = customerMapT[spk.id_customer] ?? '-';

        const isSelesai = (spk.status||'').toLowerCase().includes('selesai');
        const btnLabel = isSelesai ? 'Detail' : 'Kerjakan';
        const btnAction = isSelesai
            ? `openDetail(${JSON.stringify(spk.id_spk)})`
            : `openKerjakan(${JSON.stringify(spk.id_spk)})`;

        tbTeknisi.innerHTML += `
            <tr>
                <td>${start + i + 1}</td>
                <td>${fmtDate(spk.tanggal_spk)}</td>
                <td>${svc}</td>
                <td>${jasa}</td>
                <td>${cust}</td>
                <td>${jenis}</td>
                <td>${spk.no_kendaraan||'-'}</td>
                <td>${spk.keluhan||'-'}</td>
                <td>${spk.catatan||'-'}</td>  
                <td>${badge(spk.status)}</td>
                <td>
                    <button class="btn btn-sm ${isSelesai?'btn-outline-secondary':'btn-primary'}" onclick='${btnAction}'>${btnLabel}</button>
                </td>
            </tr>
        `;
    });

    const end = Math.min(start+perPageT, total);
    infoTeknisi.textContent = `Showing ${total? start+1:0} to ${end} of ${total} entries`;

    pagesTeknisi.innerHTML = '';
    for (let p=1;p<=Math.ceil(total/perPageT||1);p++){
        const b = document.createElement('button');
        b.className = `btn btn-sm ${p===pageT?'btn-primary':'btn-light'}`;
        b.textContent = p;
        b.onclick = ()=>{ pageT = p; filterAndRenderT(); };
        pagesTeknisi.appendChild(b);
    }
}

function nextPageT(){
    const total = filteredT.length;
    const totalPages = Math.ceil(total/perPageT)||1;
    if(pageT<totalPages){ pageT++; filterAndRenderT(); }
}
function prevPageT(){
    if(pageT>1){ pageT--; filterAndRenderT(); }
}

/** ====== MODAL HANDLERS ====== **/
let kerjakanModal;
let detailModal;
document.addEventListener('DOMContentLoaded', ()=>{
    kerjakanModal = new bootstrap.Modal(document.getElementById('kerjakanModal'),{});
    detailModal = new bootstrap.Modal(document.getElementById('detailModal'),{});
});

// kerjakan -> prefill
function openKerjakan(id_spk){
    const spk = allSpkT.find(x=> String(x.id_spk)===String(id_spk));
    if(!spk){ toastErr('SPK tidak ditemukan'); return; }

    // isi modal
    document.getElementById('k_id_spk').value   = spk.id_spk;
    document.getElementById('k_tanggal').value  = (spk.tanggal_spk||'').substring(0,10);
    document.getElementById('k_customer').value = customerMapT[spk.id_customer] || spk.id_customer || '';
    document.getElementById('k_nopol').value    = spk.no_kendaraan || '';
    document.getElementById('k_keluhan').value  = spk.keluhan || '';
    document.getElementById('k_id_jasa').value  = spk.id_jasa || '';
    document.getElementById('k_catatan').value  = spk.catatan || ''; 

    let labelText = 'sudah di proses';
    if ((spk.status||'').toLowerCase()==='di proses mekanik') labelText = 'sudah di proses';
    document.getElementById('k_status_label').innerHTML = `<span class="badge bg-primary">sudah di proses</span>`;
    document.getElementById('k_status').value = 'sudah di proses';

    kerjakanModal.show();
}

// detail -> card only
function openDetail(id_spk){
    const spk = allSpkT.find(x=> String(x.id_spk)===String(id_spk));
    if(!spk){ toastErr('SPK tidak ditemukan'); return; }

    const jasa = jasaMapT[spk.id_jasa] || '-';
    const cust = customerMapT[spk.id_customer] || '-';
    const svc  = serviceMapT[spk.id_service] || '-';

    const cards = `
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-gear-fill me-2"></i>${svc}</h5>
                    ${badge(spk.status)}
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-briefcase me-2 text-primary"></i>
                            <strong>Jasa:</strong> ${jasa}
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-person me-2 text-primary"></i>
                            <strong>Customer:</strong> ${cust}
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-car-front me-2 text-primary"></i>
                            <strong>No Kendaraan:</strong> ${spk.no_kendaraan||'-'}
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-calendar-date me-2 text-primary"></i>
                            <strong>Tanggal:</strong> ${fmtDate(spk.tanggal_spk)}
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-chat-dots me-2 text-primary"></i>
                            <strong>Keluhan:</strong> ${spk.keluhan||'-'}
                        </li>
                        <li>
                            <i class="bi bi-sticky me-2 text-primary"></i>
                            <strong>Catatan Teknisi:</strong> ${spk.catatan||'-'}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    `;

    document.getElementById('detailContent').innerHTML = cards;
    detailModal.show();
}


/** ====== HELPERS ====== **/
function parseIntOrNull(v){
    if (v === undefined || v === null || v === '') return null;
    const n = parseInt(v);
    return isNaN(n) ? null : n;
}

/** ====== SUBMIT (implementasi ke SEMUA tombol kerjakan) ====== **/
document.getElementById('btnSimpanSelesai').addEventListener('click', async ()=>{
    const id_spk = document.getElementById('k_id_spk').value;
    const id_jasa = document.getElementById('k_id_jasa').value;
    const catatan = document.getElementById('k_catatan').value ?? '';

    const spkCurr = allSpkT.find(s=> String(s.id_spk)===String(id_spk));
    if(!spkCurr){ toastErr('SPK tidak ditemukan (internal)'); return; }
    if(!id_jasa){ toastWarn('Pilih jasa terlebih dahulu'); return; }

    const tanggalInput = document.getElementById('k_tanggal').value || (spkCurr.tanggal_spk||'').substring(0,10);
    let tanggalISO = null;
    if (tanggalInput) {
        const d = new Date(tanggalInput + 'T00:00:00'); tanggalISO = d.toISOString();
    }

    // Kirim nilai 'selesai' ke DB, label tetap biru di UI
    const payload = {
        tanggal_spk: tanggalISO,
        id_service: parseIntOrNull(spkCurr.id_service),
        id_jasa: parseInt(id_jasa),
        id_customer: parseIntOrNull(spkCurr.id_customer),
        id_jenis: parseIntOrNull(spkCurr.id_jenis),
        no_kendaraan: spkCurr.no_kendaraan || '',
        keluhan: spkCurr.keluhan || '',
        catatan: catatan || '',
        status: 'selesai'
    };

    try{
        await apiSend(`${apiBase}/spk/${id_spk}`, 'PUT', payload);
        kerjakanModal.hide();
        await loadSpkT();
        toastOK('SPK ditandai selesai');
    }catch(err){
        console.error(err);
        toastErr(`Gagal simpan: ${err.message}`);
    }
});

document.getElementById('formKerjakan').addEventListener('submit', async (e)=>{
    e.preventDefault();

    const id_spk = document.getElementById('k_id_spk').value;
    const id_jasa = document.getElementById('k_id_jasa').value;
    const catatan = document.getElementById('k_catatan').value ?? '';

    const spkCurr = allSpkT.find(s=> String(s.id_spk)===String(id_spk));
    if(!spkCurr){ toastErr('SPK tidak ditemukan (internal)'); return; }
    if(!id_jasa){ toastWarn('Pilih jasa terlebih dahulu'); return; }

    const tanggalInput = document.getElementById('k_tanggal').value || (spkCurr.tanggal_spk||'').substring(0,10);
    let tanggalISO = null;
    if (tanggalInput) {
        const d = new Date(tanggalInput + 'T00:00:00'); tanggalISO = d.toISOString();
    }

    const payload = {
        tanggal_spk: tanggalISO,
        id_service: parseIntOrNull(spkCurr.id_service),
        id_jasa: parseInt(id_jasa),
        id_customer: parseIntOrNull(spkCurr.id_customer),
        id_jenis: parseIntOrNull(spkCurr.id_jenis),
        no_kendaraan: spkCurr.no_kendaraan || '',
        keluhan: spkCurr.keluhan || '',
        catatan: catatan || '',
        status: 'sudah di proses'
    };

    try{
        await apiSend(`${apiBase}/spk/${id_spk}`, 'PUT', payload);
        kerjakanModal.hide();
        await loadSpkT();
        toastOK('Perubahan disimpan');
    }catch(err){
        console.error(err);
        toastErr(`Gagal simpan: ${err.message}`);
    }
});

/** ====== EVENTS ====== **/
searchTek?.addEventListener('input', ()=>{ pageT=1; filterAndRenderT(); });
filterStatus?.addEventListener('change', ()=>{ pageT=1; filterAndRenderT(); });

/** ====== INIT ====== **/
(async function init(){
    await loadRefs();
    await loadSpkT();

    // Deep link ?id_spk=xxx -> langsung buka modal kerjakan
    const params = new URLSearchParams(location.search);
    const qid = params.get('id_spk');
    if (qid) {
        const chk = allSpkT.find(s=> String(s.id_spk)===String(qid));
        if (chk) openKerjakan(qid);
    }
})();
</script>
@endsection
