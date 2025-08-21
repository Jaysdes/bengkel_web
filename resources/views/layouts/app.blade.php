<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Bengkel Management System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'neon': {
              50: '#e6f0ff', 100: '#b3ccff', 200: '#80aaff', 300: '#4d88ff', 400: '#1a66ff',
              500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 800: '#1e3a8a', 900: '#0b1220'
            },
            'dark': {
              50: '#f8fafc', 100: '#f1f5f9', 200: '#e2e8f0', 300: '#cbd5e1', 400: '#94a3b8',
              500: '#64748b', 600: '#475569', 700: '#334155', 800: '#1e293b', 900: '#0f172a', 950: '#020617'
            }
          }
        }
      }
    }
  </script>
  
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Alpine.js -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <!-- Bootstrap CSS & JS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Custom Styles -->
  <style>
    :root {
      --neon-blue: #3b82f6;         /* Blue */
      --neon-blue-dark: #1d4ed8;    /* Darker Blue */
      --neon-blue-light: #3b82f6;   /* Lighter Blue */
      --dark-bg: #000000;           /* Black */
      --dark-surface: #0b0b0b;      /* Near black */
      --dark-card: #151515;         /* Card surface */
      --dark-border: #2b2b2b;       /* Border */
      --text-primary: #ffffff;      /* White */
      --text-secondary: #e5e7eb;    /* Gray-200 */
      --text-muted: #9ca3af; 
      --text-white: #ffffff;
    }
    body { 
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; 
    background: #ffffff; /* ubah jadi putih */
    color: #000000; /* teks jadi hitam agar kontras */
    min-height: 100vh; 
}
    .neon-glow { box-shadow: 0 0 5px var(--neon-blue), 0 0 5px var(--neon-blue), 0 0 10px var(--neon-blue), 0 0 15px var(--neon-blue); }
    .neon-text { color: var(--neon-blue); text-shadow: 0 0 5px var(--neon-blue), 0 0 10px var(--neon-blue), 0 0 15px var(--neon-blue); }
    .neon-border { border: 2px solid var(--neon-blue); box-shadow: 0 0 10px var(--neon-blue), inset 0 0 10px rgba(59, 130, 246, 0.15); }
    .sidebar-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .stat-card-hover { transition: all 0.3s ease; cursor: pointer; } .card-hover:hover { transform: translateY(-8px); }
    .btn-neon { background: linear-gradient(45deg, #0b0b0b, #151515); border: 2px solid var(--neon-blue); color: #fff; padding: 12px 24px; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 0 20px rgba(59, 130, 246, 0.35); }
    .btn-neon:hover { background: var(--neon-blue); color: #0b0b0b; box-shadow: 0 0 30px rgba(59, 130, 246, 0.6), 0 4px 15px rgba(0, 0, 0, 0.3); transform: translateY(-2px); }
    .btn-neon-solid { background: var(--neon-blue); color: #0b0b0b; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 0 20px rgba(59, 130, 246, 0.5), 0 4px 15px rgba(0, 0, 0, 0.3); }
    .btn-neon-solid:hover { background: var(--neon-blue-light); box-shadow: 0 0 40px rgba(59, 130, 246, 0.8), 0 6px 20px rgba(0, 0, 0, 0.4); transform: translateY(-3px); }
    .nav-item-neon { margin-bottom: 8px; transition: all 0.3s ease; } .nav-item-neon:hover { transform: translateX(8px); }
    .nav-link-neon { color: var(--text-secondary); padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; text-decoration: none; transition: all 0.3s ease; position: relative; overflow: hidden; background: rgba(255, 255, 255, 0.02); border: 1px solid transparent; }
    .nav-link-neon::before { content: ''; position: absolute; left: 0; top: 0; height: 100%; width: 4px; background: var(--neon-blue); transform: scaleY(0); transition: transform 0.3s ease; }
    .nav-link-neon:hover, .nav-link-neon.active { background: rgba(59, 130, 246, 0.12); color: #fff; border-color: rgba(59, 130, 246, 0.35); box-shadow: 0 0 15px rgba(59, 130, 246, 0.25); }
    .nav-link-neon.active::before { transform: scaleY(1); }
    .main-content { 
    background: #ffffff; /* putih */
    border-radius: 20px 0 0 20px; 
    min-height: auto; 
    max-height: none; 
    overflow-y: visible; 
    padding: 2rem; 
    color: #000000; 
    border-left: 1px solid #e5e7eb; /* border abu-abu terang */
}

    .main-content::-webkit-scrollbar { width: 8px; } .main-content::-webkit-scrollbar-track { background: var(--dark-surface); } .main-content::-webkit-scrollbar-thumb { background: var(--neon-blue); border-radius: 4px; } .main-content::-webkit-scrollbar-thumb:hover { background: var(--neon-blue-light); }
    @media (max-width: 768px) { .main-content { border-radius: 20px 20px 0 0; } }
    .page-title { background: linear-gradient(45deg, var(--neon-blue), var(--neon-blue-light)); background-clip: text; -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 700; font-size: 2rem; margin-bottom: 0.5rem; }
    .stat-card { background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 20px; transition: all 0.3s ease; overflow: hidden; position: relative; }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, var(--neon-blue), var(--neon-blue-light)); }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6), 0 0 30px rgba(59, 130, 246, 0.2); border-color: rgba(59, 130, 246, 0.5); }
    .stat-icon { width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1rem; background: linear-gradient(45deg, var(--neon-blue), var(--neon-blue-dark)); color: var(--dark-bg); box-shadow: 0 0 20px rgba(59, 130, 246, 0.4); }
    .hamburger { display: flex; flex-direction: column; gap: 4px; width: 24px; height: 24px; cursor: pointer; transition: all 0.3s ease; }
    .hamburger span { width: 100%; height: 3px; background: var(--neon-blue); border-radius: 2px; transition: all 0.3s ease; transform-origin: center; box-shadow: 0 0 10px rgba(59, 130, 246, 0.5); }
    @keyframes slideIn { from { transform: translateX(-100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    @keyframes fadeInUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    @keyframes neonPulse { 0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); } 50% { box-shadow: 0 0 40px rgba(59, 130, 246, 0.6); } }
    .animate-slide-in { animation: slideIn 0.3s ease-out; } .animate-fade-in-up { animation: fadeInUp 0.5s ease-out; } .animate-neon-pulse { animation: neonPulse 2s infinite; }
    .table-neon { background: var(--dark-card); border-radius: 16px; overflow: hidden; border: 1px solid var(--dark-border); }
    .table-neon thead { background: linear-gradient(45deg, var(--neon-blue), var(--neon-blue-dark)); }
    .table-neon thead th { color: var(--dark-bg); border: none; padding: 1rem; font-weight: 600; }
    .table-neon tbody tr { transition: all 0.2s ease; border-bottom: 1px solid var(--dark-border); }
    .table-neon tbody tr:hover { background: rgba(59, 130, 246, 0.08); box-shadow: inset 0 0 20px rgba(59, 130, 246, 0.12); }
    .form-neon { background: var(--dark-card); border-radius: 16px; padding: 2rem; border: 1px solid var(--dark-border); }
    .input-neon { 
    border: 2px solid #ccc; 
    border-radius: 8px; 
    padding: 12px 16px; 
    transition: all 0.3s ease; 
    background: #ffffff; /* putih */
    color: #000000;
    text-color:#ffffff; /* hitam */
}

.card-body { background: var(--white-card); border: 1px solid var(--dark-border);overflow: hidden; position: relative; }
.card-header { background: var(--white-card); border: 1px solid var(--neon-blue); border-radius: 5px; overflow: hidden; position: relative; }
.card-2{ background: var(--white-card); border: 1px solid var(--neon-blue); border-radius: 5px; overflow: hidden; position: relative; }

.input-neon:focus { 
    border-color: var(--neon-blue); 
    background: #ffffff; /* tetap putih saat fokus */
    box-shadow: 0 0 8px rgba(59, 130, 246, 0.4); 
    outline: none; 
    color: #000000; /* tetap hitam */
}
.alert-neon { border-radius: 12px; padding: 16px 20px; border: 1px solid; background: var(--dark-card); }
    .alert-success { border-color: #10b981; background: linear-gradient(45deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05)); color: #34d399; }
    .alert-error { border-color: #ef4444; background: linear-gradient(45deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05)); color: #f87171; }
    .alert-warning { border-color: #f59e0b; background: linear-gradient(45deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.05)); color: #fbbf24; }
    .alert-info { border-color: var(--neon-blue); background: linear-gradient(45deg, rgba(59, 130, 246, 0.12), rgba(59, 130, 246, 0.06)); color: var(--neon-blue); }
    .breadcrumb-neon { background: rgba(59, 130, 246, 0.12); border-radius: 12px; padding: 12px 16px; border: 1px solid rgba(59, 130, 246, 0.3); }
    ::-webkit-scrollbar { width: 8px; } ::-webkit-scrollbar-track { background: var(--dark-surface); } ::-webkit-scrollbar-thumb { background: var(--neon-blue); border-radius: 4px; } ::-webkit-scrollbar-thumb:hover { background: var(--neon-blue-light); }
    ::selection { background: rgba(59, 130, 246, 0.35); color: var(--text-primary); }
    ::placeholder { color: var(--text-muted); }
    .modal-content { background: var(--dark-card); border: 1px solid var(--dark-border); color: var(--text-primary); }
    .modal-header { border-bottom: 1px solid var(--dark-border); } .modal-footer { border-top: 1px solid var(--dark-border); }

    .btn-close { filter: invert(1); }
    .form-control, .form-select { background: var(--text-white); border: 2px solid var(--dark-border); color:var(--dark-bg); }
    .form-control:focus, .form-select:focus { background: var(--text-white); border-color: var(--neon-blue); box-shadow: 0 0 20px rgba(59, 130, 246, 0.2); color: var(--dark-bg); }
    .dropdown-menu { background: var(--dark-card); border: 1px solid var(--dark-border); }
    .dropdown-item { color: var(--text-secondary); } .dropdown-item:hover { background: rgba(59, 130, 246, 0.12); color: var(--neon-blue); }
  </style>
  
  @stack('styles')
</head>
<body class=" bg-white bg-gray-100 text-sm font-sans" x-data="{ open: true }">

  <!-- Toggle Button -->
  <div class="fixed top-4 left-4 z-50">
    <div 
      class="flex flex-col gap-2 w-10 h-10 bg-dark p-2 cursor-pointer" 
      style="border-radius: 15%;" 
      @click="open = !open"
    >
      <div :class="open 
        ? 'rotate-[225deg] -translate-x-[12px] -translate-y-[1px] origin-right w-1/2' 
        : 'w-1/2'" 
        class="transition-all duration-500 h-[3px] bg-white rounded-2xl"
      ></div>
      <div :class="open ? '-rotate-45 w-full' : 'w-full'" 
        class="transition-all duration-500 h-[3px] bg-white rounded-2xl"
      ></div>
      <div :class="open 
        ? 'rotate-[225deg] translate-x-[12px] translate-y-[1px] origin-left w-1/2 place-self-end' 
        : 'w-1/2 place-self-end'" 
        class="transition-all duration-500 h-[3px] bg-white rounded-2xl"
      ></div>
    </div>
  </div>

  <div class="flex transition-all duration-300">

    <!-- Sidebar -->
    <div 
      x-show="open"
      x-transition:enter="transition ease-out duration-200"
      x-transition:enter-start="opacity-0 -translate-x-full"
      x-transition:enter-end="opacity-100 translate-x-0"
      x-transition:leave="transition ease-in duration-200"
      x-transition:leave-start="opacity-100 translate-x-0"
      x-transition:leave-end="opacity-0 -translate-x-full"
      class="w-64 bg-gray-900 text-white shadow-lg h-screen overflow-y-auto fixed top-0 left-0 z-40"
    >
      @include('layouts.sidebar')
    </div>
    <!-- Main Content Area: margin-left automatically follows sidebar width -->
    <div class="flex-1 flex flex-col transition-all duration-300"
         :class="open ? 'ml-45 md:ml-55 lg:ml-60' : 'ml-0 md:ml-0 lg:ml-0'">
      
      <!-- Top Navigation Bar -->
      <nav class="bg-black/90 backdrop-blur-lg border-b border-gray-800 sticky top-0 z-10">
        <div class="px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
            <div class="flex items-center">
            
            </div>
            
            <!-- User Menu -->
            <div class="flex items-center space-x-4">
              <div class="text-right text-white">
                <div class="text-sm font-medium">{{ session('user')['name'] ?? 'Guest' }}</div>
                <div class="text-xs text-gray-400 capitalize">{{ ucfirst(session('user')['role'] ?? 'guest') }}</div>
              </div>
              <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center neon-glow">
                <i class="fas fa-user text-white text-sm"></i>
              </div>
            </div>
          </div>
        </div>
      </nav>

      <!-- Page Content -->
      <main class="flex-1 main-content animate-fade-in-up">
        @yield('content')
      </main>
    </div>
  </div>

  <!-- Loading Indicator -->
  <div id="loading-indicator" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="dark-card p-8 rounded-2xl neon-border">
      <div class="flex items-center space-x-4">
        <div class="animate-spin rounded-full h-8 w-8 border-4 border-blue-400 border-t-transparent neon-glow"></div>
        <span class="neon-text font-medium">Loading...</span>
      </div>
    </div>
  </div>

  <!-- Toast Container -->
  <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

  @yield('scripts')
  @stack('scripts')

  <script>
    // Global utility functions
    window.showLoading = function() { const e = document.getElementById('loading-indicator'); e.classList.remove('hidden'); e.classList.add('flex'); };
    window.hideLoading = function() { const e = document.getElementById('loading-indicator'); e.classList.add('hidden'); e.classList.remove('flex'); };
    window.showToast = function(message, type = 'info', duration = 5000) {
      const toast = document.createElement('div');
      const colors = { success: 'border-green-500 bg-green-900/20 text-green-400', error: 'border-red-500 bg-red-900/20 text-red-400', warning: 'border-yellow-500 bg-yellow-900/20 text-yellow-400', info: 'border-blue-500 bg-blue-900/20 text-blue-400' };
      const icons = { success: 'check-circle', error: 'exclamation-triangle', warning: 'exclamation-circle', info: 'info-circle' };
      toast.className = `${colors[type]} px-6 py-4 rounded-lg shadow-2xl transform transition-all duration-300 translate-x-full opacity-0 border backdrop-blur-md`;
      toast.innerHTML = `<div class="flex items-center space-x-3"><i class="fas fa-${icons[type]} text-lg"></i><span class="font-medium">${message}</span></div>`;
      document.getElementById('toast-container').appendChild(toast);
      setTimeout(() => { toast.classList.remove('translate-x-full', 'opacity-0'); }, 100);
      setTimeout(() => { toast.classList.add('translate-x-full', 'opacity-0'); setTimeout(() => toast.remove(), 300); }, duration);
    };
    window.formatCurrency = function(amount) { return new Intl.NumberFormat('id-ID').format(amount || 0); };
    window.debounce = function(func, wait) { let t; return function(...args){ const later=()=>{ clearTimeout(t); func(...args); }; clearTimeout(t); t=setTimeout(later, wait); } };
    document.addEventListener('DOMContentLoaded', function() {
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(alert => { setTimeout(() => { alert.style.transition = 'all 0.5s ease-out'; alert.style.opacity = '0'; alert.style.transform = 'translateY(-20px)'; setTimeout(() => alert.remove(), 500); }, 5000); });
      checkSessionHealth();
    });
    function checkSessionHealth() {
      fetch('{{ route("dashboard") }}', { method: 'HEAD', credentials: 'same-origin' }).catch(() => {});
    }
  </script>
</body>
</html>