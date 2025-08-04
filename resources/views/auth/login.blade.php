<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <style>
        body {
            background-image: url('https://i.pinimg.com/originals/bd/ef/31/bdef31f664010979c0c0acce2af552ea.gif');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 0;
        }

        .login-container {
            z-index: 1;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen relative">

    <div class="w-full max-w-sm bg-zinc-900 text-white rounded-lg shadow-lg overflow-hidden border border-zinc-700 login-container">
        <!-- Header Image Only (no text inside) -->
        <div class="h-32 bg-cover bg-center" style="background-image: url('https://i.pinimg.com/originals/57/2c/19/572c1921557952edc061196b83b5b0d8.gif');">
        </div>

        <!-- Form Section -->
        <div class="px-6 py-6 space-y-4">

             <!-- Log-In Text -->
    <h1 class="text-center text-2xl font-semibold tracking-wide text-white mb-2">Service APP</h1>

{{-- Alert Success (Login Berhasil) --}}
@if(session('success') && session('user'))
    <div class="bg-green-600 text-white text-sm px-4 py-2 rounded shadow-md">
        ✅ Selamat datang <strong>{{ session('user')['name'] }}</strong>! Anda login sebagai <strong>{{ session('user')['role'] }}</strong>.
    </div>
@endif

{{-- Alert Error (Login Gagal) --}}
@if(session('error'))
    <div class="bg-red-600 text-white text-sm px-4 py-2 rounded shadow-md">
        ❌ {{ session('error') }}
    </div>
@endif

{{-- Laravel Error Validation --}}
@if($errors->any())
    <div class="bg-red-600 text-white text-sm px-4 py-2 rounded shadow-md">
        ❌ {{ $errors->first() }}
    </div>
@endif

<!-- Form -->
<form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
    @csrf

    <!-- Email -->
    <div>
        <label for="email" class="text-sm font-medium text-gray-300">Username <span class="text-red-500">*</span></label>
        <div class="relative mt-1">
            <input
                class="bg-zinc-800 text-white border border-zinc-600 rounded w-full py-2 pl-10 pr-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="your@email.com"
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                required
            />
            <i class="ph ph-user absolute left-3 top-2.5 text-lg text-gray-400"></i>
        </div>
    </div>

    <!-- Password -->
    <div>
        <label for="password" class="text-sm font-medium text-gray-300">Password <span class="text-red-500">*</span></label>
        <div class="relative mt-1">
            <input
                class="bg-zinc-800 text-white border border-zinc-600 rounded w-full py-2 pl-10 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="••••••••"
                id="password"
                name="password"
                type="password"
                required
            />
            <i class="ph ph-lock-simple absolute left-3 top-2.5 text-lg text-gray-400"></i>
            <button type="button" onclick="togglePassword()" class="absolute right-3 top-2.5 text-gray-400">
                <i id="eye-icon" class="ph ph-eye text-lg"></i>
            </button>
        </div>
    </div>

    <!-- Submit -->
    <button
        type="submit"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded shadow-inner transition-colors"
    >
        LOG IN
    </button>
</form>
        </div>

        <div class="text-center text-xs text-gray-500 pb-4">
            &copy; 2025 Maintenance Service
        </div>
    </div>

    <!-- Toggle password -->
    <script>
        function togglePassword() {
            const input = document.getElementById("password");
            const icon = document.getElementById("eye-icon");

            if (input.type === "password") {            
                input.type = "text";
                icon.classList.remove("ph-eye");
                icon.classList.add("ph-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("ph-eye-slash");
                icon.classList.add("ph-eye");
            }
        }
    </script>
</body>
</html>
