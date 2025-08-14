@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="page-title">
                <i class="fas fa-tachometer-alt mr-3"></i>
                Dashboard Overview
            </h1>
            <p class="text-gray-400 text-lg">
                Selamat datang di sistem manajemen bengkel. Berikut ringkasan aktivitas hari ini.
            </p>
        </div>
        <div class="flex items-center space-x-3 mt-4 lg:mt-0">
            <div class="text-right">
                <div class="text-sm text-gray-400">{{ now()->format('l, d F Y') }}</div>
                <div class="text-xs text-gray-500" id="current-time"></div>
            </div>
            <button onclick="refreshDashboard()" class="btn-neon">
                <i class="fas fa-sync-alt"></i>
                Refresh
            </button>
        </div>
    </div>

    <!-- Date Filter Section -->
    <div class="stat-card bg-dark p-6">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between space-y-4 lg:space-y-0">
            <div>
                <h3 class="text-lg font-semibold text-white mb-2">
                    <i class="fas fa-calendar-alt mr-2 text-cyan-400"></i>
                    Filter Data by Date Range
                </h3>
                <p class="text-sm text-gray-400">Select date range to filter dashboard statistics and charts</p>
            </div>
            <div class="flex flex-col sm:flex-row items-center space-y-3 sm:space-y-0 sm:space-x-4">
                <div class="flex flex-col">
                    <label for="start_date" class="block text-sm font-medium text-gray-300 mb-1">Start Date</label>
                    <input type="date" 
                           id="start_date" 
                           name="start_date" 
                           value="{{ $startDate }}" 
                           class="input-neon w-full sm:w-auto">
                </div>
                <div class="flex flex-col">
                    <label for="end_date" class="block text-sm font-medium text-gray-300 mb-1">End Date</label>
                    <input type="date" 
                           id="end_date" 
                           name="end_date" 
                           value="{{ $endDate }}" 
                           class="input-neon w-full sm:w-auto">
                </div>
                <div class="flex flex-col justify-end">
                    <button onclick="applyDateFilter()" class="btn-neon">
                        <i class="fas fa-filter mr-2"></i>
                        Apply Filter
                    </button>
                </div>
                <div class="flex flex-col justify-end">
                    <button onclick="resetDateFilter()" class="btn btn-outline-secondary text-white border-gray-600 hover:bg-gray-600">
                        <i class="fas fa-undo mr-2"></i>
                        Reset
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Current Filter Display -->
        <div class="mt-4 pt-4 border-t border-gray-700">
            <div class="flex items-center text-sm text-gray-400">
                <i class="fas fa-info-circle mr-2 text-cyan-400"></i>
                <span>Currently showing data from <strong class="text-white">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</strong> to <strong class="text-white">{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Users Card -->
        <div class="stat-card card-hover group bg-dark">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="stat-icon group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white">{{ $userCount }}</h3>
                        <p class="text-gray-300 font-medium">Total Users</p>
                        <p class="text-xs text-gray-500 mt-1">Registered users</p>
                    </div>
                    <div class="text-cyan-400 opacity-20 group-hover:opacity-40 transition-opacity duration-300">
                        <i class="fas fa-users text-4xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Card -->
        <div class="stat-card card-hover group bg-dark">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="stat-icon group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-cash-register"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white">{{ $transaksiCount }}</h3>
                        <p class="text-gray-300 font-medium">Transaksi</p>
                        <p class="text-xs text-gray-500 mt-1">Total transactions</p>
                    </div>
                    <div class="text-cyan-400 opacity-20 group-hover:opacity-40 transition-opacity duration-300">
                        <i class="fas fa-cash-register text-4xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- SPK Card -->
        <div class="stat-card card-hover group bg-dark">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="stat-icon group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white">{{ $spkCount }}</h3>
                        <p class="text-gray-300 font-medium">SPK Aktif</p>
                        <p class="text-xs text-gray-500 mt-1">Work orders</p>
                    </div>
                    <div class="text-cyan-400 opacity-20 group-hover:opacity-40 transition-opacity duration-300">
                        <i class="fas fa-file-alt text-4xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Processes Card -->
        <div class="stat-card card-hover group bg-dark">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="stat-icon group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white">{{ $prosesCount }}</h3>
                        <p class="text-gray-300 font-medium">Proses Aktif</p>
                        <p class="text-xs text-gray-500 mt-1">Active processes</p>
                    </div>
                    <div class="text-cyan-400 opacity-20 group-hover:opacity-40 transition-opacity duration-300">
                        <i class="fas fa-cogs text-4xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Distribution Chart -->
        <div class="stat-card bg-dark">
            <div class="p-6 border-b border-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Data Distribution</h3>
                        <p class="text-sm text-gray-400">Overview of system data</p>
                    </div>
                    <div class="stat-icon w-10 h-10">
                        <i class="fas fa-chart-pie text-sm"></i>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div style="height: 300px;" class="relative">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Trend Chart -->
        <div class="stat-card bg-dark">
            <div class="p-6 border-b border-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Transaction Trends</h3>
                        <p class="text-sm text-gray-400">Weekly transaction analysis</p>
                    </div>
                    <div class="stat-icon w-10 h-10">
                        <i class="fas fa-chart-line text-sm"></i>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div style="height: 300px;" class="relative">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Transactions -->
        <div class="lg:col-span-2 stat-card bg-dark">
            <div class="p-6 border-b border-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Recent Activity</h3>
                        <p class="text-sm text-gray-400">Latest system activities</p>
                    </div>
                    <a href="{{ route('laporan') }}" class="neon-text hover:text-cyan-300 text-sm font-medium transition-colors">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center space-x-4 p-4 bg-gray-800/50 rounded-lg border border-gray-700">
                        <div class="w-10 h-10 bg-cyan-500/20 rounded-lg flex items-center justify-center neon-glow">
                            <i class="fas fa-user text-cyan-400"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-white">User Management</p>
                            <p class="text-sm text-gray-400">{{ $userCount }} users registered</p>
                        </div>
                        <div class="text-xs text-gray-500">
                            Just now
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4 p-4 bg-gray-800/50 rounded-lg border border-gray-700">
                        <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center neon-glow">
                            <i class="fas fa-cash-register text-green-400"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-white">Transactions</p>
                            <p class="text-sm text-gray-400">{{ $transaksiCount }} completed</p>
                        </div>
                        <div class="text-xs text-gray-500">
                            2 mins ago
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4 p-4 bg-gray-800/50 rounded-lg border border-gray-700">
                        <div class="w-10 h-10 bg-yellow-500/20 rounded-lg flex items-center justify-center neon-glow">
                            <i class="fas fa-file-alt text-yellow-400"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-white">Work Orders</p>
                            <p class="text-sm text-gray-400">{{ $spkCount }} active SPK</p>
                        </div>
                        <div class="text-xs text-gray-500">
                            5 mins ago
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access Panel -->
        <div class="stat-card bg-dark">
            <div class="p-6 border-b border-gray-800">
                <h3 class="text-lg font-semibold text-white">Quick Actions</h3>
                <p class="text-sm text-gray-400">Frequently used features</p>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @if(in_array(session('user')['role'] ?? 'guest', ['admin', 'keuangan']))
                    <a href="{{ route('transaksi') }}" class="flex items-center p-3 bg-cyan-500/10 hover:bg-cyan-500/20 rounded-lg transition-colors group border border-cyan-500/20">
                        <div class="w-8 h-8 bg-gradient-to-r from-cyan-500 to-blue-600 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform neon-glow">
                            <i class="fas fa-plus text-black text-sm"></i>
                        </div>
                        <div>
                            <p class="font-medium text-white">New Transaction</p>
                            <p class="text-xs text-gray-400">Create new service</p>
                        </div>
                    </a>
                    @endif

                    @if(in_array(session('user')['role'] ?? 'guest', ['admin', 'gudang']))
                    <a href="{{ route('sparepart') }}" class="flex items-center p-3 bg-green-500/10 hover:bg-green-500/20 rounded-lg transition-colors group border border-green-500/20">
                        <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform neon-glow">
                            <i class="fas fa-cogs text-black text-sm"></i>
                        </div>
                        <div>
                            <p class="font-medium text-white">Manage Parts</p>
                            <p class="text-xs text-gray-400">Inventory control</p>
                        </div>
                    </a>
                    @endif

                    <a href="{{ route('laporan') }}" class="flex items-center p-3 bg-purple-500/10 hover:bg-purple-500/20 rounded-lg transition-colors group border border-purple-500/20">
                        <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-violet-600 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform neon-glow">
                            <i class="fas fa-chart-bar text-black text-sm"></i>
                        </div>
                        <div>
                            <p class="font-medium text-white">View Reports</p>
                            <p class="text-xs text-gray-400">Analytics dashboard</p>
                        </div>
                    </a>

                    <div class="pt-3 border-t border-gray-800">
                        <button onclick="showSystemInfo()" class="flex items-center p-3 bg-gray-500/10 hover:bg-gray-500/20 rounded-lg transition-colors w-full group border border-gray-500/20">
                            <div class="w-8 h-8 bg-gradient-to-r from-gray-500 to-slate-600 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform neon-glow">
                                <i class="fas fa-info text-black text-sm"></i>
                            </div>
                            <div class="text-left">
                                <p class="font-medium text-white">System Info</p>
                                <p class="text-xs text-gray-400">Version details</p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="stat-card bg-dark">
        <div class="p-6 border-b border-gray-800">
            <h3 class="text-lg font-semibold text-white">System Status</h3>
            <p class="text-sm text-gray-400">Current system health and performance</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="w-12 h-12 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-2 neon-glow">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <p class="font-medium text-white">API Status</p>
                    <p class="text-xs text-green-400">Online</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-2 neon-glow">
                        <i class="fas fa-database text-cyan-400"></i>
                    </div>
                    <p class="font-medium text-white">Database</p>
                    <p class="text-xs text-cyan-400">Connected</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-yellow-500/20 rounded-full flex items-center justify-center mx-auto mb-2 neon-glow">
                        <i class="fas fa-server text-yellow-400"></i>
                    </div>
                    <p class="font-medium text-white">Server Load</p>
                    <p class="text-xs text-yellow-400">Normal</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-purple-500/20 rounded-full flex items-center justify-center mx-auto mb-2 neon-glow">
                        <i class="fas fa-clock text-purple-400"></i>
                    </div>
                    <p class="font-medium text-white">Uptime</p>
                    <p class="text-xs text-purple-400">99.9%</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Info Modal -->
<div class="modal fade" id="systemInfoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 dark-card neon-border">
            <div class="modal-header bg-gradient-to-r from-cyan-500 to-blue-600 text-black">
                <h5 class="modal-title font-bold">
                    <i class="fas fa-info-circle mr-2"></i>
                    System Information
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-400">Application Name:</span>
                        <span class="text-white">Bengkel Management System</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-400">Version:</span>
                        <span class="text-cyan-400">v2.0.0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-400">Framework:</span>
                        <span class="text-white">Laravel 12</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-400">Database:</span>
                        <span class="text-white">SQLite</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-400">Last Updated:</span>
                        <span class="text-white">{{ now()->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    updateClock();
    setInterval(updateClock, 1000);
    setupDateFilterListeners();
});

function initializeCharts() {
    // Pie Chart
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Users', 'Transaksi', 'SPK', 'Proses'],
            datasets: [{
                data: [{{ $userCount }}, {{ $transaksiCount }}, {{ $spkCount }}, {{ $prosesCount }}],
                backgroundColor: [
                    'rgba(6, 182, 212, 0.8)',
                    'rgba(16, 185, 129, 0.8)', 
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(139, 92, 246, 0.8)'
                ],
                borderColor: [
                    'rgba(6, 182, 212, 1)',
                    'rgba(16, 185, 129, 1)',
                    'rgba(245, 158, 11, 1)', 
                    'rgba(139, 92, 246, 1)'
                ],
                borderWidth: 2,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        color: 'white',
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.9)',
                    titleColor: '#00ffff',
                    bodyColor: 'white',
                    borderColor: '#00ffff',
                    borderWidth: 1,
                    cornerRadius: 8
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true
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
                borderColor: 'rgba(6, 182, 212, 1)',
                backgroundColor: 'rgba(6, 182, 212, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 6,
                pointHoverRadius: 8,
                pointBackgroundColor: 'rgba(6, 182, 212, 1)',
                pointBorderColor: 'black',
                pointBorderWidth: 2,
                shadowColor: 'rgba(6, 182, 212, 0.5)',
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowOffsetY: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        padding: 20,
                        color: 'white',
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.9)',
                    titleColor: '#00ffff',
                    bodyColor: 'white',
                    borderColor: '#00ffff',
                    borderWidth: 1,
                    cornerRadius: 8
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: {
                            size: 11
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(75, 85, 99, 0.3)',
                        borderDash: [5, 5]
                    },
                    ticks: {
                        stepSize: 1,
                        color: '#9ca3af',
                        font: {
                            size: 11
                        }
                    }
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeOutQuart'
            }
        }
    });
}

function updateClock() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('id-ID');
    const timeElement = document.getElementById('current-time');
    if (timeElement) {
        timeElement.textContent = timeString;
    }
}

function refreshDashboard() {
    showLoading();
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

function showSystemInfo() {
    const modal = new bootstrap.Modal(document.getElementById('systemInfoModal'));
    modal.show();
}

// Date Filter Functions
function applyDateFilter() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    // Validate dates
    if (!startDate || !endDate) {
        showToast('Please select both start and end dates', 'warning');
        return;
    }
    
    if (new Date(startDate) > new Date(endDate)) {
        showToast('Start date cannot be later than end date', 'error');
        return;
    }
    
    // Show loading and apply filter
    showLoading();
    const baseUrl = "{{ route('dashboard') }}";
    const filterUrl = `${baseUrl}?start_date=${startDate}&end_date=${endDate}`;
    
    // Add a small delay for better UX
    setTimeout(() => {
        window.location.href = filterUrl;
    }, 500);
}

function resetDateFilter() {
    showLoading();
    const baseUrl = "{{ route('dashboard') }}";
    
    // Reset to default date range (last 7 days to today)
    setTimeout(() => {
        window.location.href = baseUrl;
    }, 500);
}

// Auto-apply filter when dates change (optional enhancement)
function setupDateFilterListeners() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    // Add change listeners for real-time validation
    startDateInput.addEventListener('change', function() {
        const endDate = endDateInput.value;
        if (endDate && new Date(this.value) > new Date(endDate)) {
            showToast('Start date cannot be later than end date', 'warning');
            this.focus();
        }
    });
    
    endDateInput.addEventListener('change', function() {
        const startDate = startDateInput.value;
        if (startDate && new Date(startDate) > new Date(this.value)) {
            showToast('End date cannot be earlier than start date', 'warning');
            this.focus();
        }
    });
}

// Animate stats on page load
document.addEventListener('DOMContentLoaded', function() {
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

// Welcome message for new login
@if(session('success') && session('user'))
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        showToast('Selamat datang {{ session("user")["name"] }}! Login berhasil.', 'success');
    }, 500);
});
@endif

// Enhanced session management
function maintainSession() {
    // Keep session alive with periodic health checks
    setInterval(() => {
        fetch('{{ route("dashboard") }}', { 
            method: 'HEAD',
            credentials: 'same-origin',
            cache: 'no-cache'
        }).catch(error => {
            console.log('Session check failed:', error);
        });
    }, 300000); // Check every 5 minutes
}

// Initialize session maintenance
document.addEventListener('DOMContentLoaded', function() {
    maintainSession();
});
</script>
@endsection