@php
    $current = request()->path();
    $pengaturanActive = in_array($current, ['service-center', 'users', 'mekanik']);
    $role = session('user')['role'] ?? 'guest';
@endphp

{{-- Bootstrap Icons --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div class="w-64 bg-dark text-white shadow-lg p-3" style="min-height: 100vh;">
    {{-- Header --}}
    <div class="mb-4 text-center border-bottom pb-3">
        @if(session('user'))
            <div class="d-flex flex-column align-items-center">
                <i class="bi bi-person-circle fs-2 mb-1"></i>
                <strong class="text-white">{{ session('user')['name'] }}</strong>
                <span class="badge bg-warning text-dark mt-1 text-capitalize">
                    {{ $role }}
                </span>
            </div>
        @else
            <p class="text-white">Anda belum login.</p>
        @endif
    </div>

    {{-- Menu Utama --}}
    <ul class="nav flex-column">
        {{-- Home - semua role --}}
        <li class="nav-item mb-1">
            <a href="{{ route('dashboard') }}"
               class="nav-link d-flex align-items-center {{ $current == 'dashboard' ? 'bg-warning text-dark fw-bold rounded' : 'text-white hover:bg-secondary rounded' }}">
                <i class="bi bi-house me-2"></i> Home
            </a>
        </li>

        {{-- Data - admin & customer --}}
        @if(in_array($role, ['admin', 'customer']))
        <li class="nav-item mb-1">
            <a href="{{ route('data') }}"
               class="nav-link d-flex align-items-center {{ $current == 'data' ? 'bg-warning text-dark fw-bold rounded' : 'text-white hover:bg-secondary rounded' }}">
                <i class="bi bi-folder2-open me-2"></i> Data
            </a>
        </li>
        @endif
        {{-- SPK - admin & mekanik --}}
@if(in_array($role, ['admin', 'mekanik']))
<li class="nav-item mb-1">
    <a href="{{ route('spk') }}"
       class="nav-link d-flex align-items-center {{ $current == 'spk' ? 'bg-warning text-dark fw-bold rounded' : 'text-white hover:bg-secondary rounded' }}">
        <i class="bi bi-journal-check me-2"></i> SPK
    </a>
</li>
@endif


        {{-- Teknisi - admin & mekanik --}}
        @if(in_array($role, ['admin', 'mekanik']))
        <li class="nav-item mb-1">
            <a href="{{ route('teknisi') }}"
               class="nav-link d-flex align-items-center {{ $current == 'teknisi' ? 'bg-warning text-dark fw-bold rounded' : 'text-white hover:bg-secondary rounded' }}">
                <i class="bi bi-people me-2"></i> Teknisi
            </a>
        </li>
        @endif

        {{-- Proses Berjalan - admin, customer, mekanik --}}
        @if(in_array($role, ['admin', 'customer', 'mekanik']))
        <li class="nav-item mb-1">
            <a href="{{ route('proses') }}"
               class="nav-link d-flex align-items-center {{ $current == 'proses' ? 'bg-warning text-dark fw-bold rounded' : 'text-white hover:bg-secondary rounded' }}">
                <i class="bi bi-arrow-repeat me-2"></i> Proses Berjalan
            </a>
        </li>
        @endif

        {{-- Laporan - admin, customer, mekanik --}}
        @if(in_array($role, ['admin', 'customer', 'mekanik']))
        <li class="nav-item mb-1">
            <a href="{{ route('laporan') }}"
               class="nav-link d-flex align-items-center {{ $current == 'laporan' ? 'bg-warning text-dark fw-bold rounded' : 'text-white hover:bg-secondary rounded' }}">
                <i class="bi bi-file-earmark-text me-2"></i> Laporan
            </a>
        </li>
        @endif

        {{-- Sparepart - admin & gudang --}}
        @if(in_array($role, ['admin', 'gudang']))
        <li class="nav-item mb-1">
            <a href="{{ route('sparepart') }}"
               class="nav-link d-flex align-items-center {{ $current == 'sparepart' ? 'bg-warning text-dark fw-bold rounded' : 'text-white hover:bg-secondary rounded' }}">
                <i class="bi bi-cpu me-2"></i> Sparepart
            </a>
        </li>
        @endif

        {{-- Transaksi - admin & keuangan --}}
        @if(in_array($role, ['admin', 'keuangan']))
        <li class="nav-item mb-1">
            <a href="{{ route('transaksi') }}"
               class="nav-link d-flex align-items-center {{ $current == 'transaksi' ? 'bg-warning text-dark fw-bold rounded' : 'text-white hover:bg-secondary rounded' }}">
                <i class="bi bi-cash-stack me-2"></i> Transaksi
            </a>
        </li>
        @endif
    </ul>

    {{-- Dropdown Pengaturan - admin & mekanik --}}
    @if(in_array($role, ['admin', 'mekanik']))
    <div class="mt-3" x-data="{ open: {{ $pengaturanActive ? 'true' : 'false' }} }">
        <button
            class="btn btn-outline-light w-100 d-flex align-items-center justify-content-between mb-2"
            @click="open = !open">
            <span><i class="bi bi-gear me-2"></i> Pengaturan</span>
            <i :class="open ? 'bi bi-chevron-up' : 'bi bi-chevron-down'"></i>
        </button>

        <div x-show="open" x-transition class="mt-2">
            <ul class="nav flex-column ms-3 p-2 rounded bg-secondary bg-opacity-25">
               
                @if($role === 'admin')
                <li class="nav-item mb-1">
                    <a href="{{ route('users') }}"
                       class="nav-link d-flex align-items-center {{ $current == 'users' ? 'bg-warning text-dark fw-bold rounded' : 'text-white hover:bg-dark rounded' }}">
                        <i class="bi bi-person-badge me-2"></i> Manajemen Users
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="{{ route('mekanik') }}"
                       class="nav-link d-flex align-items-center {{ $current == 'mekanik' ? 'bg-warning text-dark fw-bold rounded' : 'text-white hover:bg-dark rounded' }}">
                        <i class="bi bi-tools me-2"></i> Manajemen Mekanik
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </div>
    @endif

    {{-- Logout --}}
    <div class="mt-4">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger w-100">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </button>
        </form>
    </div>
</div>
