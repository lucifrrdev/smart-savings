<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log In - Smart Savings</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FDFDFC] text-[#1b1b18] min-h-screen flex items-center justify-center p-6 font-sans">

    <div class="w-full max-w-[400px]">
        <!-- Brand Logo -->
        <div class="flex flex-col items-center mb-8">
            <div class="bg-[#f53003] text-white p-3 rounded-2xl mb-4 shadow-[0_4px_12px_rgba(245,48,3,0.2)]">
                <i class="fa-solid fa-wallet text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">Smart Savings</h1>
            <p class="text-xs text-[#706f6c] mt-1">Masuk untuk mengelola tabungan Anda</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl p-8 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-[#19140010]">
            
            @if ($errors->any())
                <div class="mb-5 p-3.5 bg-rose-50 border border-rose-100 rounded-xl text-rose-600 text-xs">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li><i class="fa-solid fa-circle-exclamation mr-1.5"></i>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf
                
                <!-- Username -->
                <div>
                    <label for="username" class="block text-xs font-semibold text-[#706f6c] uppercase tracking-wider mb-2">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400">
                            <i class="fa-solid fa-user text-sm"></i>
                        </span>
                        <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus
                               class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#f53003] focus:ring-1 focus:ring-[#f53003] transition-all" 
                               placeholder="Masukkan username">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-[#706f6c] uppercase tracking-wider mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </span>
                        <input type="password" name="password" id="password" required
                               class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#f53003] focus:ring-1 focus:ring-[#f53003] transition-all" 
                               placeholder="••••••••">
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" 
                           class="w-4 h-4 rounded border-gray-300 text-[#f53003] focus:ring-[#f53003]">
                    <label for="remember" class="ml-2 text-xs text-[#706f6c]">Ingat saya di perangkat ini</label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-3 bg-[#f53003] hover:bg-[#d62700] text-white font-semibold rounded-xl text-sm shadow-[0_2px_8px_rgba(245,48,3,0.15)] transition-all">
                    Masuk ke Akun
                </button>
            </form>
        </div>

        <!-- Register Link / Button -->
        <p class="text-center text-xs text-[#706f6c] mt-6">
            Belum punya akun? 
            <button type="button" onclick="openRegisterRoleModal()" class="font-semibold text-[#f53003] hover:underline cursor-pointer bg-transparent border-0 p-0">Daftar sekarang</button>
        </p>
    </div>

    <!-- Modal Pilih Peran Pendaftaran -->
    <div id="registerRoleModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <!-- Backdrop -->
            <div onclick="closeRegisterRoleModal()" class="fixed inset-0 bg-black/50 transition-opacity backdrop-blur-xs" aria-hidden="true"></div>

            <!-- Modal Card -->
            <div class="relative bg-white rounded-3xl p-6 sm:p-8 text-left shadow-2xl transform transition-all w-full max-w-[460px] border border-gray-100 z-10">
                <div class="flex justify-between items-start mb-5">
                    <div>
                        <div class="inline-flex p-2.5 rounded-xl bg-orange-50 text-[#f53003] mb-2">
                            <i class="fa-solid fa-user-plus text-lg"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900" id="modal-title">Pilih Jenis Akun</h3>
                        <p class="text-xs text-gray-500 mt-1">Silakan pilih jenis peran untuk pendaftaran akun Anda</p>
                    </div>
                    <button type="button" onclick="closeRegisterRoleModal()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <div class="space-y-3.5 my-6">
                    <!-- Option: Siswa -->
                    <a href="{{ route('register', ['role' => 'siswa']) }}" 
                       class="group flex items-start gap-4 p-4 rounded-2xl border-2 border-gray-100 hover:border-[#f53003] hover:bg-orange-50/20 transition-all text-left">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-graduation-cap text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-sm text-gray-900 group-hover:text-[#f53003] transition-colors">Siswa</span>
                                <span class="text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-md bg-blue-100/70 text-blue-700">Role Siswa</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Daftar dengan NISN, nama lengkap, dan kelas untuk mulai menabung.</p>
                        </div>
                    </a>

                    <!-- Option: Wali Kelas -->
                    <a href="{{ route('register', ['role' => 'wali_kelas']) }}" 
                       class="group flex items-start gap-4 p-4 rounded-2xl border-2 border-gray-100 hover:border-[#f53003] hover:bg-orange-50/20 transition-all text-left">
                        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-chalkboard-user text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-sm text-gray-900 group-hover:text-[#f53003] transition-colors">Wali Kelas</span>
                                <span class="text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-md bg-purple-100/70 text-purple-700">Role Admin</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Daftar dengan NIP dan penugasan kelas untuk mengelola tabungan kelas.</p>
                        </div>
                    </a>
                </div>

                <div class="pt-2 text-center border-t border-gray-100">
                    <button type="button" onclick="closeRegisterRoleModal()" class="text-xs text-gray-500 hover:text-gray-700 font-medium">
                        Kembali ke Halaman Login
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openRegisterRoleModal() {
            document.getElementById('registerRoleModal').classList.remove('hidden');
        }
        function closeRegisterRoleModal() {
            document.getElementById('registerRoleModal').classList.add('hidden');
        }
    </script>

</body>
</html>
