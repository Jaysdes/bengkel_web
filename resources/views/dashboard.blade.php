@extends('layouts.app')

@section('content')

    <div class="row">

        <!-- Main Content -->
        <div class="col-lg-12">
            <h4 class="mb-4 fw-bold text-info"><i class="fas fa-chart-bar"></i> Dashboard Ringkasan</h4>

            <!-- Ringkasan Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card bg-dark text-white neon-shadow h-100">
                        <div class="card-body text-center">
                            <div class="mb-2"><i class="fas fa-users fa-2x text-info"></i></div>
                            <h6 class="fw-bold">User</h6>
                            <h3>{{ $userCount }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-dark text-white neon-shadow h-100">
                        <div class="card-body text-center">
                            <div class="mb-2"><i class="fas fa-cash-register fa-2x text-success"></i></div>
                            <h6 class="fw-bold">Transaksi</h6>
                            <h3>{{ $transaksiCount }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-dark text-white neon-shadow h-100">
                        <div class="card-body text-center">
                            <div class="mb-2"><i class="fas fa-file-alt fa-2x text-warning"></i></div>
                            <h6 class="fw-bold">SPK</h6>
                            <h3>{{ $spkCount }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-dark text-white neon-shadow h-100">
                        <div class="card-body text-center">
                            <div class="mb-2"><i class="fas fa-tasks fa-2x text-secondary"></i></div>
                            <h6 class="fw-bold">Proses</h6>
                            <h3>{{ $prosesCount }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card bg-dark text-white neon-shadow h-100">
                        <div class="card-header fw-bold border-bottom border-info">
                            <i class="fas fa-chart-pie text-info"></i> Distribusi Data
                        </div>
                        <div class="card-body" style="height:300px;">
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card bg-dark text-white neon-shadow h-100">
                        <div class="card-header fw-bold border-bottom border-info">
                            <i class="fas fa-chart-line text-info"></i> Grafik Tren Transaksi Harian
                        </div>
                        <div class="card-body" style="height:300px;">
                            <canvas id="lineChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Tabel -->
            <div class="card bg-dark text-white neon-shadow border-0">
                <div class="card-header fw-bold border-bottom border-info">
                    <i class="fas fa-table text-info"></i> Tabel Ringkasan Data
                </div>
                <div class="card-body p-0">
                    <table class="table table-dark table-striped table-bordered mb-0 text-white">
                        <thead class="table-light text-dark">
                            <tr>
                                <th>No</th>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>1</td><td>User</td><td>{{ $userCount }}</td></tr>
                            <tr><td>2</td><td>Transaksi</td><td>{{ $transaksiCount }}</td></tr>
                            <tr><td>3</td><td>SPK</td><td>{{ $spkCount }}</td></tr>
                            <tr><td>4</td><td>Proses</td><td>{{ $prosesCount }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

@endsection

@push('styles')
<style>
    body {
        background-color: #000;
        color: #fff;
    }

    .neon-shadow {
        box-shadow:
            0 8px 15px rgba(0, 240, 255, 0.15), /* shadow bawah */
            0 0 15px rgba(0, 240, 255, 0.3),     /* glow */
            0 0 30px rgba(0, 240, 255, 0.1);     /* glow luar */
        border-radius: 15px;
        transform: translateY(0);
        transition: all 0.3s ease-in-out;
        background-color: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
    }

    .neon-shadow:hover {
        transform: translateY(-8px) scale(1.03);
        box-shadow:
            0 12px 30px rgba(0, 240, 255, 0.3),
            0 0 25px rgba(0, 240, 255, 0.6),
            0 0 40px rgba(0, 240, 255, 0.4);
    }

    .card-header {
        background: transparent;
        border-bottom: 1px solid #00f0ff33;
    }

    .table-dark th, .table-dark td {
        border-color: #0ff;
    }

    .table-dark {
        background-color: rgba(0,0,0,0.5);
    }
</style>
@endpush

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Pie Chart
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: ['User', 'Transaksi', 'SPK', 'Proses'],
            datasets: [{
                data: [{{ $userCount }}, {{ $transaksiCount }}, {{ $spkCount }}, {{ $prosesCount }}],
                backgroundColor: ['#17a2b8', '#28a745', '#ffc107', '#6c757d'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: 'white'
                    }
                }
            }
        }
    });

    // Line Chart
    const lineCtx = document.getElementById('lineChart').getContext('2d');
    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Transaksi Harian',
                data: {!! json_encode($chartData) !!},
                borderColor: '#00f0ff',
                backgroundColor: 'rgba(0,240,255,0.2)',
                tension: 0.3,
                fill: true,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    ticks: { color: '#fff' }
                },
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, color: '#fff' }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: 'white'
                    }
                }
            }
        }
    });
</script>
@endsection
