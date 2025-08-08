<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  

  
</head>
<body class="bg-gray-100 text-sm font-sans" x-data="{ open: true }">

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

    <!-- Main Content -->
    <div 
      :class="open ? 'ml-64' : 'ml-16'" 
      class="flex-1 transition-all duration-300 p-6"
    >
      @yield('content')
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  @yield('scripts')
</body>
</html>
