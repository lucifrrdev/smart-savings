<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun - Smart Savings</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FDFDFC] text-[#1b1b18] min-h-screen flex items-center justify-center p-4 sm:p-6 font-sans">

    <div class="w-full max-w-[540px] my-6">
        <!-- Brand Logo -->
        <div class="flex flex-col items-center mb-6 text-center">
            <div class="bg-[#f53003] text-white p-3 rounded-2xl mb-3 shadow-[0_4px_12px_rgba(245,48,3,0.2)]">
                <i class="fa-solid fa-user-plus text-xl"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">Buat Akun Baru</h1>
            <p class="text-xs text-[#706f6c] mt-1" id="headerSubtitle">
                Pilih peran dan lengkapi data untuk mulai menggunakan Smart Savings
            </p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-[0_4px_24px_rgba(0,0,0,0.04)] border border-[#19140010]">
            
            @php
                $activeRole = old('role_type', $role ?? 'siswa');
            @endphp

            <!-- Role Selector Tabs -->
            <div class="mb-6">
                <label class="block text-xs font-semibold text-[#706f6c] uppercase tracking-wider mb-2">Daftar Sebagai</label>
                <div class="grid grid-cols-2 p-1.5 bg-gray-100 rounded-xl border border-gray-200 gap-1.5">
                    <button type="button" 
                            id="btnRoleSiswa"
                            onclick="setRole('siswa')"
                            class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-lg text-xs font-bold transition-all cursor-pointer {{ $activeRole === 'siswa' ? 'bg-white text-[#f53003] shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        <i class="fa-solid fa-graduation-cap text-sm"></i>
                        <span>Siswa</span>
                    </button>
                    <button type="button" 
                            id="btnRoleWali"
                            onclick="setRole('wali_kelas')"
                            class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-lg text-xs font-bold transition-all cursor-pointer {{ $activeRole === 'wali_kelas' ? 'bg-white text-[#f53003] shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        <i class="fa-solid fa-chalkboard-user text-sm"></i>
                        <span>Wali Kelas</span>
                    </button>
                </div>
            </div>

            <!-- Role Info Banner -->
            <div id="bannerSiswa" class="{{ $activeRole === 'siswa' ? '' : 'hidden' }} mb-5 p-3.5 bg-blue-50/80 border border-blue-100 rounded-xl text-blue-700 text-xs flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0 text-blue-600">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div>
                    <span class="font-bold">Mode Siswa:</span>
                    <p class="text-[11px] text-blue-600/90 mt-0.5">Akun akan otomatis didaftarkan dengan hak akses sebagai <b>Role Siswa</b>.</p>
                </div>
            </div>

            <div id="bannerWali" class="{{ $activeRole === 'wali_kelas' ? '' : 'hidden' }} mb-5 p-3.5 bg-purple-50/80 border border-purple-100 rounded-xl text-purple-700 text-xs flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center shrink-0 text-purple-600">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <div>
                    <span class="font-bold">Mode Wali Kelas:</span>
                    <p class="text-[11px] text-purple-600/90 mt-0.5">Akun akan otomatis didaftarkan dengan hak akses sebagai <b>Role Admin</b>.</p>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-5 p-3.5 bg-rose-50 border border-rose-100 rounded-xl text-rose-600 text-xs">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li><i class="fa-solid fa-circle-exclamation mr-1.5"></i>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-4" id="registerForm">
                @csrf
                <input type="hidden" name="role_type" id="role_type" value="{{ $activeRole }}">
                
                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-[#706f6c] uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fa-solid fa-address-card text-xs"></i>
                        </span>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                               class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#f53003] focus:ring-1 focus:ring-[#f53003] transition-all" 
                               placeholder="Nama Lengkap">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Dynamic Class Selection -->
                    <div id="fieldClassSiswa" class="{{ $activeRole === 'siswa' ? '' : 'hidden' }}">
                        <label for="class_siswa" class="block text-xs font-semibold text-[#706f6c] uppercase tracking-wider mb-1.5">Kelas</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <i class="fa-solid fa-graduation-cap text-xs"></i>
                            </span>
                            <select name="class" id="class_siswa" {{ $activeRole === 'siswa' ? 'required' : 'disabled' }}
                                    class="w-full pl-9 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#f53003] focus:ring-1 focus:ring-[#f53003] transition-all appearance-none">
                                <option value="" disabled {{ old('class') ? '' : 'selected' }}>Pilih Kelas</option>
                                <option value="Kelas 4" {{ old('class') == 'Kelas 4' ? 'selected' : '' }}>Kelas 4</option>
                                <option value="Kelas 5" {{ old('class') == 'Kelas 5' ? 'selected' : '' }}>Kelas 5</option>
                                <option value="Kelas 6" {{ old('class') == 'Kelas 6' ? 'selected' : '' }}>Kelas 6</option>
                            </select>
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-chevron-down text-[10px]"></i>
                            </span>
                        </div>
                    </div>

                    <div id="fieldClassWali" class="{{ $activeRole === 'wali_kelas' ? '' : 'hidden' }}">
                        <label for="class_wali" class="block text-xs font-semibold text-[#706f6c] uppercase tracking-wider mb-1.5">Wali Kelas Berapa</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <i class="fa-solid fa-chalkboard text-xs"></i>
                            </span>
                            <select name="class" id="class_wali" {{ $activeRole === 'wali_kelas' ? 'required' : 'disabled' }}
                                    class="w-full pl-9 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#f53003] focus:ring-1 focus:ring-[#f53003] transition-all appearance-none">
                                <option value="" disabled {{ old('class') ? '' : 'selected' }}>Pilih Wali Kelas</option>
                                <option value="Wali Kelas 4" {{ old('class') == 'Wali Kelas 4' ? 'selected' : '' }}>Wali Kelas 4</option>
                                <option value="Wali Kelas 5" {{ old('class') == 'Wali Kelas 5' ? 'selected' : '' }}>Wali Kelas 5</option>
                                <option value="Wali Kelas 6" {{ old('class') == 'Wali Kelas 6' ? 'selected' : '' }}>Wali Kelas 6</option>
                            </select>
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-chevron-down text-[10px]"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Dynamic NISN vs NIP -->
                    <div id="fieldNisn" class="{{ $activeRole === 'siswa' ? '' : 'hidden' }}">
                        <label for="nisn" class="block text-xs font-semibold text-[#706f6c] uppercase tracking-wider mb-1.5">NISN</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <i class="fa-solid fa-id-badge text-xs"></i>
                            </span>
                            <input type="text" name="nisn" id="nisn" value="{{ old('nisn') }}" {{ $activeRole === 'siswa' ? 'required' : 'disabled' }}
                                   class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#f53003] focus:ring-1 focus:ring-[#f53003] transition-all" 
                                   placeholder="10 digit NISN">
                        </div>
                    </div>

                    <div id="fieldNip" class="{{ $activeRole === 'wali_kelas' ? '' : 'hidden' }}">
                        <label for="nip" class="block text-xs font-semibold text-[#706f6c] uppercase tracking-wider mb-1.5">NIP</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <i class="fa-solid fa-id-card text-xs"></i>
                            </span>
                            <input type="text" name="nip" id="nip" value="{{ old('nip') }}" {{ $activeRole === 'wali_kelas' ? 'required' : 'disabled' }}
                                   class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#f53003] focus:ring-1 focus:ring-[#f53003] transition-all" 
                                   placeholder="Nomor Induk Pegawai (NIP)">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="birth_date" class="block text-xs font-semibold text-[#706f6c] uppercase tracking-wider mb-1.5">Tanggal Lahir</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <i class="fa-solid fa-calendar text-xs"></i>
                            </span>
                            <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" required
                                   class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#f53003] focus:ring-1 focus:ring-[#f53003] transition-all">
                        </div>
                    </div>

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-xs font-semibold text-[#706f6c] uppercase tracking-wider mb-1.5">Username</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <i class="fa-solid fa-user text-xs"></i>
                            </span>
                            <input type="text" name="username" id="username" value="{{ old('username') }}" required
                                   class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#f53003] focus:ring-1 focus:ring-[#f53003] transition-all" 
                                   placeholder="Username untuk login">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-[#706f6c] uppercase tracking-wider mb-1.5">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <i class="fa-solid fa-lock text-xs"></i>
                            </span>
                            <input type="password" name="password" id="password" required
                                   pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*?&#]).{8,}"
                                   title="Password minimal 8 karakter dengan kombinasi huruf besar, kecil, angka, dan simbol (@$!%*?&#)."
                                   class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#f53003] focus:ring-1 focus:ring-[#f53003] transition-all" 
                                   placeholder="Min. 8 karakter">
                        </div>
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-[#706f6c] uppercase tracking-wider mb-1.5">Konfirmasi Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <i class="fa-solid fa-shield-halved text-xs"></i>
                            </span>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                   class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#f53003] focus:ring-1 focus:ring-[#f53003] transition-all" 
                                   placeholder="Ulangi password">
                        </div>
                    </div>
                </div>

                <div class="text-[11px] text-gray-500 bg-gray-50 p-2.5 rounded-lg border border-gray-100 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-gray-400"></i>
                    <span>Password wajib minimal 8 karakter (kombinasi huruf besar, kecil, angka, simbol @$!%*?&#).</span>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        id="submitBtn"
                        class="w-full py-3 mt-2 bg-[#f53003] hover:bg-[#d62700] text-white font-semibold rounded-xl text-sm shadow-[0_2px_10px_rgba(245,48,3,0.2)] transition-all cursor-pointer">
                    {{ $activeRole === 'wali_kelas' ? 'Daftar Sebagai Wali Kelas' : 'Daftar Sebagai Siswa' }}
                </button>
            </form>
        </div>

        <!-- Login Link -->
        <p class="text-center text-xs text-[#706f6c] mt-6">
            Sudah punya akun? 
            <a href="{{ route('login') }}" class="font-semibold text-[#f53003] hover:underline">Masuk di sini</a>
        </p>
    </div>

    <script>
        function setRole(role) {
            const roleInput = document.getElementById('role_type');
            const btnSiswa = document.getElementById('btnRoleSiswa');
            const btnWali = document.getElementById('btnRoleWali');
            const bannerSiswa = document.getElementById('bannerSiswa');
            const bannerWali = document.getElementById('bannerWali');
            const fieldClassSiswa = document.getElementById('fieldClassSiswa');
            const fieldClassWali = document.getElementById('fieldClassWali');
            const selectClassSiswa = document.getElementById('class_siswa');
            const selectClassWali = document.getElementById('class_wali');
            const fieldNisn = document.getElementById('fieldNisn');
            const fieldNip = document.getElementById('fieldNip');
            const inputNisn = document.getElementById('nisn');
            const inputNip = document.getElementById('nip');
            const submitBtn = document.getElementById('submitBtn');

            roleInput.value = role;

            if (role === 'wali_kelas') {
                // Update tabs style
                btnWali.className = 'flex items-center justify-center gap-2 py-2.5 px-3 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white text-[#f53003] shadow-sm';
                btnSiswa.className = 'flex items-center justify-center gap-2 py-2.5 px-3 rounded-lg text-xs font-bold transition-all cursor-pointer text-gray-600 hover:text-gray-900';

                // Banners
                bannerSiswa.classList.add('hidden');
                bannerWali.classList.remove('hidden');

                // Class inputs
                fieldClassSiswa.classList.add('hidden');
                selectClassSiswa.disabled = true;
                selectClassSiswa.removeAttribute('required');

                fieldClassWali.classList.remove('hidden');
                selectClassWali.disabled = false;
                selectClassWali.setAttribute('required', 'required');

                // Identification (NIP vs NISN)
                fieldNisn.classList.add('hidden');
                inputNisn.disabled = true;
                inputNisn.removeAttribute('required');

                fieldNip.classList.remove('hidden');
                inputNip.disabled = false;
                inputNip.setAttribute('required', 'required');

                // Submit button
                submitBtn.innerText = 'Daftar Sebagai Wali Kelas';
            } else {
                // Update tabs style
                btnSiswa.className = 'flex items-center justify-center gap-2 py-2.5 px-3 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white text-[#f53003] shadow-sm';
                btnWali.className = 'flex items-center justify-center gap-2 py-2.5 px-3 rounded-lg text-xs font-bold transition-all cursor-pointer text-gray-600 hover:text-gray-900';

                // Banners
                bannerWali.classList.add('hidden');
                bannerSiswa.classList.remove('hidden');

                // Class inputs
                fieldClassWali.classList.add('hidden');
                selectClassWali.disabled = true;
                selectClassWali.removeAttribute('required');

                fieldClassSiswa.classList.remove('hidden');
                selectClassSiswa.disabled = false;
                selectClassSiswa.setAttribute('required', 'required');

                // Identification (NISN vs NIP)
                fieldNip.classList.add('hidden');
                inputNip.disabled = true;
                inputNip.removeAttribute('required');

                fieldNisn.classList.remove('hidden');
                inputNisn.disabled = false;
                inputNisn.setAttribute('required', 'required');

                // Submit button
                submitBtn.innerText = 'Daftar Sebagai Siswa';
            }

            // Sync URL query without refresh
            const url = new URL(window.location);
            url.searchParams.set('role', role);
            window.history.replaceState({}, '', url);
        }
    </script>
</body>
</html>
