@extends('layouts.app')

@section('content')

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
@endif

@php
    $role = session('user')['role'] ?? 'guest';
@endphp

<style>
    body {
        background: linear-gradient(135deg, #1e293b, #0f172a);
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(10px);
        border-radius: 1rem;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .glass-card:hover {
        transform: scale(1.03);
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.3);
    }

    .glass-icon {
        background: rgba(255, 255, 255, 0.12);
        padding: 12px;
        border-radius: 50%;
    }
</style>

<div class="p-6 min-h-screen text-white">
    <h1 class="text-3xl font-bold mb-6 text-center">Dashboard</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- Card Template --}}
        @php
            $cards = [];

            if (in_array($role, ['admin', 'customer'])) {
                $cards[] = ['label' => 'Data', 'icon' => '📁', 'route' => 'data'];
            }
            if (in_array($role, ['admin', 'mekanik'])) {
                $cards[] = ['label' => 'Teknisi', 'icon' => '🛠️', 'route' => 'teknisi'];
            }
            if (in_array($role, ['admin', 'customer', 'mekanik'])) {
                $cards[] = ['label' => 'Proses Berjalan', 'icon' => '🚧', 'route' => 'proses'];
                $cards[] = ['label' => 'Laporan', 'icon' => '📊', 'route' => 'laporan'];
            }
            if (in_array($role, ['admin', 'gudang'])) {
                $cards[] = ['label' => 'Sparepart', 'icon' => '🔩', 'route' => 'sparepart'];
            }
            if (in_array($role, ['admin', 'keuangan'])) {
                $cards[] = ['label' => 'Transaksi', 'icon' => '💳', 'route' => 'transaksi'];
            }
            if ($role == 'admin') {
                $cards[] = ['label' => 'Manajemen User', 'icon' => '👥', 'route' => 'users'];
                $cards[] = ['label' => 'Manajemen Mekanik', 'icon' => '🧑‍🔧', 'route' => 'mekanik'];
            }

            array_unshift($cards, ['label' => 'Home', 'icon' => '🏠', 'route' => 'home']);
        @endphp

        @foreach($cards as $card)
        <a href="{{ route($card['route']) }}">
            <div class="glass-card p-6 h-full flex flex-col justify-between">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="glass-icon text-2xl">{{ $card['icon'] }}</div>
                    <h3 class="text-xl font-semibold">{{ $card['label'] }}</h3>
                </div>
                <p class="text-sm text-gray-300">Klik untuk membuka halaman {{ strtolower($card['label']) }}.</p>
            </div>
        </a>
        @endforeach

    </div>
</div>

@endsection
