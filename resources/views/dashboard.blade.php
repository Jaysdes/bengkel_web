@extends('layouts.app')

@section('content')

{{-- Notifikasi Login Berhasil --}}
@if(session('success') && session('user'))
    <div id="loginAlert" class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-600 text-white px-6 py-3 rounded shadow-lg z-50 animate-fade-in">
        ✅ Selamat datang <strong>{{ session('user')['name'] }}</strong>! Anda login sebagai <strong>{{ session('user')['role'] }}</strong>.
    </div>

    <script>
        setTimeout(() => {
            const alert = document.getElementById('loginAlert');
            if (alert) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = 0;
                setTimeout(() => alert.remove(), 500);
            }
        }, 4000);
    </script>

    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.5s ease-out;
        }
    </style>
@endif

@php
    $role = session('user')['role'] ?? 'guest';
@endphp

<div class="p-6 min-h-screen text-white">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Home --}}
        <a href="{{ route('home') }}">
            <div class="bg-gray-800 rounded-lg p-4 shadow hover:scale-[1.02] transition transform duration-300">
                <div class="flex items-center mb-2">
                    <svg class="h-6 w-6 mr-2 text-purple-400" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 9.75L12 4l9 5.75v8.25a2 2 0 01-2 2h-2.5a2 2 0 01-2-2v-4.25a2 2 0 00-2-2H9.5a2 2 0 00-2 2V20a2 2 0 01-2 2H5a2 2 0 01-2-2V9.75z"/>
                    </svg>
                    <h3 class="font-bold text-lg">Home</h3>
                </div>
                <p>Beranda utama yang menampilkan informasi ringkas aplikasi.</p>
            </div>
        </a>

        {{-- Data --}}
        @if(in_array($role, ['admin', 'customer']))
        <a href="{{ route('data') }}">
            <div class="bg-gray-800 rounded-lg p-4 shadow hover:scale-[1.02] transition transform duration-300">
                <div class="flex items-center mb-2">
                    <svg class="h-6 w-6 mr-2 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <h3 class="font-bold text-lg">Data</h3>
                </div>
                <p>Kelola semua data utama sistem dengan mudah dan efisien.</p>
            </div>
        </a>
        @endif

        {{-- Teknisi --}}
        @if(in_array($role, ['admin', 'mekanik']))
        <a href="{{ route('teknisi') }}">
            <div class="bg-gray-800 rounded-lg p-4 shadow hover:scale-[1.02] transition transform duration-300">
                <div class="flex items-center mb-2">
                    <svg class="h-6 w-6 mr-2 text-blue-400" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M5.121 17.804A4 4 0 117.5 20h.528a4.992 4.992 0 003.972-2.44L13 16.75M9 13V9a3 3 0 116 0v4" />
                    </svg>
                    <h3 class="font-bold text-lg">Teknisi</h3>
                </div>
                <p>Manajemen teknisi dan proses kerja lapangan.</p>
            </div>
        </a>
        @endif

        {{-- Proses Berjalan --}}
        @if(in_array($role, ['admin', 'customer', 'mekanik']))
        <a href="{{ route('proses') }}">
            <div class="bg-gray-800 rounded-lg p-4 shadow hover:scale-[1.02] transition transform duration-300">
                <div class="flex items-center mb-2">
                    <svg class="h-6 w-6 mr-2 text-teal-400" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4 4v5h.582M20 20v-5h-.581M9 19v-4a3 3 0 013-3h0a3 3 0 013 3v4M17 9V7a5 5 0 00-10 0v2" />
                    </svg>
                    <h3 class="font-bold text-lg">Proses Berjalan</h3>
                </div>
                <p>Pantau service yang sedang berlangsung secara real-time.</p>
            </div>
        </a>
        @endif

        {{-- Laporan --}}
        @if(in_array($role, ['admin', 'customer', 'mekanik']))
        <a href="{{ route('laporan') }}">
            <div class="bg-gray-800 rounded-lg p-4 shadow hover:scale-[1.02] transition transform duration-300">
                <div class="flex items-center mb-2">
                    <svg class="h-6 w-6 mr-2 text-green-400" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 17v-6h6v6m2 4H7a2 2 0 01-2-2V5a2 2 0 012-2h5l2 2h5a2 2 0 012 2v12a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="font-bold text-lg">Laporan</h3>
                </div>
                <p>Lihat dan cetak laporan kerja dan transaksi.</p>
            </div>
        </a>
        @endif

        {{-- Sparepart --}}
        @if(in_array($role, ['admin', 'gudang']))
        <a href="{{ route('sparepart') }}">
            <div class="bg-gray-800 rounded-lg p-4 shadow hover:scale-[1.02] transition transform duration-300">
                <div class="flex items-center mb-2">
                    <svg class="h-6 w-6 mr-2 text-yellow-400" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 17v-6h6v6m2 4H7a2 2 0 01-2-2V5a2 2 0 012-2h5l2 2h5a2 2 0 012 2v12a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="font-bold text-lg">Sparepart</h3>
                </div>
                <p>Kelola data sparepart motor, stok, dan kategori suku cadang.</p>
            </div>
        </a>
        @endif

        {{-- Transaksi --}}
        @if(in_array($role, ['admin', 'keuangan']))
        <a href="{{ route('transaksi') }}">
            <div class="bg-gray-800 rounded-lg p-4 shadow hover:scale-[1.02] transition transform duration-300">
                <div class="flex items-center mb-2">
                    <svg class="h-6 w-6 mr-2 text-red-400" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 10h11M9 21V3M17 16h4M17 12h4M17 8h4" />
                    </svg>
                    <h3 class="font-bold text-lg">Transaksi</h3>
                </div>
                <p>Pantau dan kelola transaksi penjualan dan servis pelanggan.</p>
            </div>
        </a>
        @endif

        {{-- Data Service Center --}}
        @if(in_array($role, ['admin', 'mekanik']))
        <a href="{{ route('service-center') }}">
            <div class="bg-gray-800 rounded-lg p-4 shadow hover:scale-[1.02] transition transform duration-300">
                <div class="flex items-center mb-2">
                    <svg class="h-6 w-6 mr-2 text-orange-400" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9.75 3v3.75M14.25 3v3.75M4.5 9.75h15m-1.5 10.5H6a1.5 1.5 0 01-1.5-1.5V9.75h15v9a1.5 1.5 0 01-1.5 1.5z" />
                    </svg>
                    <h3 class="font-bold text-lg">Data Service Center</h3>
                </div>
                <p>Atur informasi service center, lokasi, dan jadwal operasional.</p>
            </div>
        </a>
        @endif

        {{-- Manajemen User --}}
        @if($role == 'admin')
        <a href="{{ route('users') }}">
            <div class="bg-gray-800 rounded-lg p-4 shadow hover:scale-[1.02] transition transform duration-300">
                <div class="flex items-center mb-2">
                    <svg class="h-6 w-6 mr-2 text-pink-400" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M5.121 17.804A4 4 0 117.5 20h.528a4.992 4.992 0 003.972-2.44L13 16.75" />
                    </svg>
                    <h3 class="font-bold text-lg">Manajemen User</h3>
                </div>
                <p>Kelola pengguna, hak akses, dan informasi login sistem.</p>
            </div>
        </a>
        @endif

        {{-- Manajemen Mekanik --}}
        @if($role == 'admin')
        <a href="{{ route('mekanik') }}">
            <div class="bg-gray-800 rounded-lg p-4 shadow hover:scale-[1.02] transition transform duration-300">
                <div class="flex items-center mb-2">
                    <svg class="h-6 w-6 mr-2 text-cyan-400" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 10h11M9 21V3M17 16h4M17 12h4M17 8h4" />
                    </svg>
                    <h3 class="font-bold text-lg">Manajemen Mekanik</h3>
                </div>
                <p>Kelola daftar mekanik, tugas, dan keahlian teknis mereka.</p>
            </div>
        </a>
        @endif

    </div>
</div>
@endsection
