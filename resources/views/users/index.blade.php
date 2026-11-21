@extends('layouts.app')

@section('title', 'Kontrol Hak Akses - Smart Savings')

@section('content')
<!-- Header Section -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl lg:text-3xl font-bold tracking-tight mb-2 text-[#1b1b18] dark:text-white">Manajemen Hak Akses</h1>
        <p class="text-sm text-[#706f6c] dark:text-zinc-400">Kontrol peran pengguna aplikasi, termasuk siswa dan administrator sistem secara manual.</p>
    </div>
    <div>
        <button onclick="openAddUserModal()" class="flex items-center gap-2 px-4 py-2.5 bg-[#f53003] hover:bg-[#d62700] text-white font-semibold rounded-xl text-sm shadow-[0_2px_8px_rgba(245,48,3,0.15)] transition-all cursor-pointer">
            <i class="fa-solid fa-plus"></i> Tambah User Baru
        </button>
    </div>
</div>

<!-- Alerts -->
@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/30 rounded-xl text-emerald-600 dark:text-emerald-400 text-sm flex items-center">
        <i class="fa-solid fa-circle-check mr-2 text-lg"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/30 rounded-xl text-rose-600 dark:text-rose-400 text-sm flex items-center">
        <i class="fa-solid fa-circle-exclamation mr-2 text-lg"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/30 rounded-xl text-rose-600 dark:text-rose-400 text-sm">
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Table Card -->
<div class="bg-white dark:bg-zinc-900 rounded-xl shadow-[0_1px_3px_0_rgba(0,0,0,0.05)] border border-[#19140010] dark:border-zinc-800 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-zinc-800/40 text-[#706f6c] dark:text-zinc-400 text-xs font-semibold uppercase border-b border-gray-100 dark:border-zinc-800">
                    <th class="px-6 py-4">No</th>
                    <th class="px-6 py-4">Nama Lengkap</th>
                    <th class="px-6 py-4">Username</th>
                    <th class="px-6 py-4">Kelas</th>
                    <th class="px-6 py-4">NISN</th>
                    <th class="px-6 py-4">Tanggal Lahir</th>
                    <th class="px-6 py-4">Peran (Role)</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#19140010] dark:divide-zinc-800 text-sm">
                @forelse($users as $index => $user)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="px-6 py-4 text-xs text-[#706f6c] dark:text-zinc-400 font-medium">
                            {{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-[#1b1b18] dark:text-white">
                            {{ $user->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-[#706f6c] dark:text-zinc-400">
                            {{ $user->username }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-[#706f6c] dark:text-zinc-400">
                            {{ $user->class }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-[#706f6c] dark:text-zinc-400">
                            {{ $user->role === 'siswa' ? ($user->nisn ?? '-') : ($user->nip ?? $user->nisn ?? '-') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-[#706f6c] dark:text-zinc-400">
                            {{ $user->birth_date ? $user->birth_date->format('d M Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-lg inline-block
                                {{ $user->role === 'superadmin' ? 'bg-purple-50 text-purple-700 dark:bg-purple-950/20 dark:text-purple-400 border border-purple-100 dark:border-purple-900/30' : 
                                   ($user->role === 'admin' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/20 dark:text-blue-400 border border-blue-100 dark:border-blue-900/30' : 
                                   'bg-gray-50 text-gray-700 dark:bg-zinc-800 dark:text-zinc-300 border border-gray-100 dark:border-zinc-700') }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center space-x-2">
                            <button onclick='openEditUserModal(@json($user))' class="text-amber-600 hover:text-amber-800 dark:text-amber-400 dark:hover:text-amber-300 text-sm font-medium transition-colors cursor-pointer inline-flex items-center">
                                <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                            </button>
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-800 dark:text-rose-400 dark:hover:text-rose-300 text-sm font-medium transition-colors cursor-pointer inline-flex items-center">
                                    <i class="fa-solid fa-trash-can mr-1"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-[#706f6c] dark:text-zinc-400">
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <i class="fa-solid fa-users-slash text-3xl text-gray-300"></i>
                                <span>Tidak ada user lain terdaftar.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Container -->
    @if($users->hasPages())
        <div class="px-6 py-4 border-t border-[#19140010] dark:border-zinc-800 bg-gray-50/50 dark:bg-zinc-800/40">
            {{ $users->links() }}
        </div>
    @endif
</div>

<!-- ==============================================
     MODAL TAMBAH USER
     ============================================== -->
<div id="addUserModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div onclick="closeAddUserModal()" class="fixed inset-0 bg-black/50 transition-opacity" aria-hidden="true"></div>

        <!-- Trick to center modal in desktop -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <div class="inline-block align-bottom bg-white dark:bg-zinc-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100 dark:border-zinc-800">
            <div class="p-6">
                <!-- Header -->
                <div class="flex justify-between items-center pb-4 border-b border-gray-100 dark:border-zinc-800">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white" id="modal-title">Tambah User Baru</h3>
                        <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Lengkapi formulir di bawah ini dengan struktur yang sama seperti halaman registrasi.</p>
                    </div>
                    <button onclick="closeAddUserModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                @php
                    $initRole = old('role', 'siswa');
                @endphp

                <!-- Role Selector Tabs -->
                <div class="mt-4 mb-4">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2">Pilih Peran User</label>
                    <div class="grid grid-cols-3 p-1.5 bg-gray-100 dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 gap-1.5">
                        <button type="button" 
                                id="btnAddRoleSiswa"
                                onclick="setAddRole('siswa')"
                                class="flex items-center justify-center gap-2 py-2 px-3 rounded-lg text-xs font-bold transition-all cursor-pointer {{ $initRole === 'siswa' ? 'bg-white dark:bg-zinc-700 text-[#f53003] shadow-sm' : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white' }}">
                            <i class="fa-solid fa-graduation-cap text-xs"></i>
                            <span>Siswa</span>
                        </button>
                        <button type="button" 
                                id="btnAddRoleWali"
                                onclick="setAddRole('admin')"
                                class="flex items-center justify-center gap-2 py-2 px-3 rounded-lg text-xs font-bold transition-all cursor-pointer {{ $initRole === 'admin' ? 'bg-white dark:bg-zinc-700 text-[#f53003] shadow-sm' : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white' }}">
                            <i class="fa-solid fa-chalkboard-user text-xs"></i>
                            <span>Wali Kelas</span>
                        </button>
                        <button type="button" 
                                id="btnAddRoleSuper"
                                onclick="setAddRole('superadmin')"
                                class="flex items-center justify-center gap-2 py-2 px-3 rounded-lg text-xs font-bold transition-all cursor-pointer {{ $initRole === 'superadmin' ? 'bg-white dark:bg-zinc-700 text-[#f53003] shadow-sm' : 'text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white' }}">
                            <i class="fa-solid fa-user-shield text-xs"></i>
                            <span>Superadmin</span>
                        </button>
                    </div>
                </div>

                <!-- Role Info Banners -->
                <div id="addBannerSiswa" class="{{ $initRole === 'siswa' ? '' : 'hidden' }} mb-4 p-3 bg-blue-50/80 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/30 rounded-xl text-blue-700 dark:text-blue-400 text-xs flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center shrink-0 text-blue-600 dark:text-blue-400">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <span class="font-bold">Mode Siswa:</span>
                        <p class="text-[11px] text-blue-600/90 dark:text-blue-400/80 mt-0.5">Akun akan didaftarkan sebagai Siswa dengan hak akses melihat tabungan sendiri.</p>
                    </div>
                </div>

                <div id="addBannerWali" class="{{ $initRole === 'admin' ? '' : 'hidden' }} mb-4 p-3 bg-purple-50/80 dark:bg-purple-950/20 border border-purple-100 dark:border-purple-900/30 rounded-xl text-purple-700 dark:text-purple-400 text-xs flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center shrink-0 text-purple-600 dark:text-purple-400">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <div>
                        <span class="font-bold">Mode Wali Kelas (Admin):</span>
                        <p class="text-[11px] text-purple-600/90 dark:text-purple-400/80 mt-0.5">Akun akan didaftarkan sebagai Wali Kelas (Admin) untuk kelas yang diampu.</p>
                    </div>
                </div>

                <div id="addBannerSuper" class="{{ $initRole === 'superadmin' ? '' : 'hidden' }} mb-4 p-3 bg-amber-50/80 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/30 rounded-xl text-amber-700 dark:text-amber-400 text-xs flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center shrink-0 text-amber-600 dark:text-amber-400">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <div>
                        <span class="font-bold">Mode Superadmin:</span>
                        <p class="text-[11px] text-amber-600/90 dark:text-amber-400/80 mt-0.5">Akun akan memiliki hak akses penuh ke seluruh kelas, transaksi, dan kontrol pengguna.</p>
                    </div>
                </div>

                <!-- Form -->
                <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="_modal" value="add">
                    <input type="hidden" name="role" id="add_role" value="{{ $initRole }}">

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="add_name" class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 dark:text-zinc-500">
                                <i class="fa-solid fa-address-card text-xs"></i>
                            </span>
                            <input type="text" name="name" id="add_name" value="{{ old('name') }}" required
                                   class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all" 
                                   placeholder="Nama Lengkap">
                        </div>
                    </div>

                    <!-- Dynamic Kelas & Dynamic NISN / NIP -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Dynamic Class Selection -->
                        <div id="addFieldClassSiswa" class="{{ $initRole === 'siswa' ? '' : 'hidden' }}">
                            <label for="add_class_siswa" class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Kelas</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 dark:text-zinc-500">
                                    <i class="fa-solid fa-graduation-cap text-xs"></i>
                                </span>
                                <select name="class" id="add_class_siswa" {{ $initRole === 'siswa' ? 'required' : 'disabled' }}
                                        class="w-full pl-9 pr-10 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all appearance-none cursor-pointer">
                                    <option value="" disabled {{ old('class') ? '' : 'selected' }}>Pilih Kelas</option>
                                    <option value="Kelas 4" {{ old('class') == 'Kelas 4' ? 'selected' : '' }}>Kelas 4</option>
                                    <option value="Kelas 5" {{ old('class') == 'Kelas 5' ? 'selected' : '' }}>Kelas 5</option>
                                    <option value="Kelas 6" {{ old('class') == 'Kelas 6' ? 'selected' : '' }}>Kelas 6</option>
                                </select>
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400 dark:text-zinc-500">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </span>
                            </div>
                        </div>

                        <div id="addFieldClassWali" class="{{ $initRole === 'admin' ? '' : 'hidden' }}">
                            <label for="add_class_wali" class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Wali Kelas Berapa</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 dark:text-zinc-500">
                                    <i class="fa-solid fa-chalkboard text-xs"></i>
                                </span>
                                <select name="class" id="add_class_wali" {{ $initRole === 'admin' ? 'required' : 'disabled' }}
                                        class="w-full pl-9 pr-10 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all appearance-none cursor-pointer">
                                    <option value="" disabled {{ old('class') ? '' : 'selected' }}>Pilih Wali Kelas</option>
                                    <option value="Wali Kelas 4" {{ old('class') == 'Wali Kelas 4' ? 'selected' : '' }}>Wali Kelas 4</option>
                                    <option value="Wali Kelas 5" {{ old('class') == 'Wali Kelas 5' ? 'selected' : '' }}>Wali Kelas 5</option>
                                    <option value="Wali Kelas 6" {{ old('class') == 'Wali Kelas 6' ? 'selected' : '' }}>Wali Kelas 6</option>
                                </select>
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400 dark:text-zinc-500">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </span>
                            </div>
                        </div>

                        <div id="addFieldClassSuper" class="{{ $initRole === 'superadmin' ? '' : 'hidden' }}">
                            <label for="add_class_super" class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Penugasan / Jabatan</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 dark:text-zinc-500">
                                    <i class="fa-solid fa-briefcase text-xs"></i>
                                </span>
                                <input type="text" name="class" id="add_class_super" value="{{ old('class', 'Super Admin') }}" {{ $initRole === 'superadmin' ? 'required' : 'disabled' }}
                                       class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all"
                                       placeholder="Contoh: Super Admin / IT Support">
                            </div>
                        </div>

                        <!-- Dynamic NISN vs NIP -->
                        <div id="addFieldNisn" class="{{ $initRole === 'siswa' ? '' : 'hidden' }}">
                            <label for="add_nisn" class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">NISN</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 dark:text-zinc-500">
                                    <i class="fa-solid fa-id-badge text-xs"></i>
                                </span>
                                <input type="text" name="nisn" id="add_nisn" value="{{ old('nisn') }}" {{ $initRole === 'siswa' ? 'required' : 'disabled' }}
                                       class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all" 
                                       placeholder="10 digit NISN">
                            </div>
                        </div>

                        <div id="addFieldNip" class="{{ $initRole === 'siswa' ? 'hidden' : '' }}">
                            <label for="add_nip" id="labelAddNip" class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">{{ $initRole === 'superadmin' ? 'NIP (OPSIONAL)' : 'NIP' }}</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 dark:text-zinc-500">
                                    <i class="fa-solid fa-id-card text-xs"></i>
                                </span>
                                <input type="text" name="nip" id="add_nip" value="{{ old('nip') }}" {{ $initRole === 'siswa' ? 'disabled' : '' }} {{ $initRole === 'admin' ? 'required' : '' }}
                                       class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all" 
                                       placeholder="{{ $initRole === 'superadmin' ? 'Nomor Induk Pegawai (opsional)' : 'Nomor Induk Pegawai (NIP)' }}">
                            </div>
                        </div>
                    </div>

                    <!-- Tanggal Lahir & Username -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Tanggal Lahir -->
                        <div>
                            <label for="add_birth_date" class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Tanggal Lahir</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 dark:text-zinc-500">
                                    <i class="fa-solid fa-calendar text-xs"></i>
                                </span>
                                <input type="date" name="birth_date" id="add_birth_date" value="{{ old('birth_date') }}" required
                                       class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all">
                            </div>
                        </div>

                        <!-- Username -->
                        <div>
                            <label for="add_username" class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Username</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 dark:text-zinc-500">
                                    <i class="fa-solid fa-user text-xs"></i>
                                </span>
                                <input type="text" name="username" id="add_username" value="{{ old('username') }}" required
                                       class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all" 
                                       placeholder="Username untuk login">
                        </div>
                    </div>

                    <!-- Password & Konfirmasi Password -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Password -->
                        <div>
                            <label for="add_password" class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Password</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 dark:text-zinc-500">
                                    <i class="fa-solid fa-lock text-xs"></i>
                                </span>
                                <input type="password" name="password" id="add_password" required
                                       pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*?&#]).{8,}"
                                       title="Password minimal 8 karakter dengan kombinasi huruf besar, kecil, angka, dan simbol (@$!%*?&#)."
                                       class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all" 
                                       placeholder="Min. 8 karakter">
                            </div>
                        </div>

                        <!-- Konfirmasi Password -->
                        <div>
                            <label for="add_password_confirmation" class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Konfirmasi Password</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 dark:text-zinc-500">
                                    <i class="fa-solid fa-shield-halved text-xs"></i>
                                </span>
                                <input type="password" name="password_confirmation" id="add_password_confirmation" required
                                       class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all" 
                                       placeholder="Ulangi password">
                            </div>
                        </div>
                    </div>

                    <!-- Password Helper Notice -->
                    <div class="text-[11px] text-gray-500 dark:text-zinc-400 bg-gray-50 dark:bg-zinc-800/60 p-2.5 rounded-lg border border-gray-100 dark:border-zinc-700/60 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-gray-400 dark:text-zinc-500"></i>
                        <span>Password wajib minimal 8 karakter (kombinasi huruf besar, kecil, angka, simbol @$!%*?&#).</span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-zinc-800">
                        <button type="button" onclick="closeAddUserModal()" class="px-4 py-2 border border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-800 rounded-xl text-sm font-semibold text-gray-700 dark:text-zinc-300 transition-all cursor-pointer">Batal</button>
                        <button type="submit" id="addSubmitBtn" class="px-5 py-2 bg-[#f53003] hover:bg-[#d62700] text-white font-semibold rounded-xl text-sm shadow-[0_2px_8px_rgba(245,48,3,0.2)] transition-all cursor-pointer">
                            @if($initRole === 'admin')
                                Daftar Sebagai Wali Kelas
                            @elseif($initRole === 'superadmin')
                                Daftar Sebagai Superadmin
                            @else
                                Daftar Sebagai Siswa
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================
     MODAL EDIT USER
     ============================================== -->
<div id="editUserModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div onclick="closeEditUserModal()" class="fixed inset-0 bg-black/50 transition-opacity" aria-hidden="true"></div>

        <!-- Trick to center modal in desktop -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <div class="inline-block align-bottom bg-white dark:bg-zinc-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100 dark:border-zinc-800">
            <div class="p-6">
                <div class="flex justify-between items-center pb-4 border-b border-gray-100 dark:border-zinc-800">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white" id="modal-title">Edit Data User</h3>
                        <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Ubah informasi user atau reset password jika diperlukan.</p>
                    </div>
                    <button onclick="closeEditUserModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form id="editUserForm" action="" method="POST" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 dark:text-zinc-500">
                                <i class="fa-solid fa-address-card text-xs"></i>
                            </span>
                            <input type="text" name="name" id="edit_name" required class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Username</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 dark:text-zinc-500">
                                    <i class="fa-solid fa-user text-xs"></i>
                                </span>
                                <input type="text" name="username" id="edit_username" required class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all">
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-1.5">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Password Baru</label>
                                <span class="text-[10px] text-gray-400">Opsional</span>
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 dark:text-zinc-500">
                                    <i class="fa-solid fa-lock text-xs"></i>
                                </span>
                                <input type="password" name="password" id="edit_password" placeholder="Isi hanya untuk ganti" class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Kelas / Penugasan</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 dark:text-zinc-500">
                                    <i class="fa-solid fa-graduation-cap text-xs"></i>
                                </span>
                                <input type="text" name="class" id="edit_class" required placeholder="Contoh: Kelas 4 / Wali Kelas 4" class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all">
                            </div>
                        </div>
                        <div>
                            <label id="edit_id_label" class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">NISN / NIP</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 dark:text-zinc-500">
                                    <i class="fa-solid fa-id-badge text-xs"></i>
                                </span>
                                <input type="text" name="nisn" id="edit_nisn" class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Tanggal Lahir</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 dark:text-zinc-500">
                                    <i class="fa-solid fa-calendar text-xs"></i>
                                </span>
                                <input type="date" name="birth_date" id="edit_birth_date" required class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Peran (Role)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 dark:text-zinc-500">
                                    <i class="fa-solid fa-shield-halved text-xs"></i>
                                </span>
                                <select name="role" id="edit_role" onchange="updateEditRoleLabel(this.value)" required class="w-full pl-9 pr-10 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all appearance-none cursor-pointer">
                                    <option value="siswa">Siswa</option>
                                    <option value="admin">Wali Kelas (Admin)</option>
                                    <option value="superadmin">Superadmin</option>
                                </select>
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400 dark:text-zinc-500">
                                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-zinc-800">
                        <button type="button" onclick="closeEditUserModal()" class="px-4 py-2 border border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-800 rounded-xl text-sm font-semibold text-gray-700 dark:text-zinc-300 transition-all cursor-pointer">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-xl text-sm transition-all cursor-pointer">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function setAddRole(role) {
        const roleInput = document.getElementById('add_role');
        const btnSiswa = document.getElementById('btnAddRoleSiswa');
        const btnWali = document.getElementById('btnAddRoleWali');
        const btnSuper = document.getElementById('btnAddRoleSuper');

        const bannerSiswa = document.getElementById('addBannerSiswa');
        const bannerWali = document.getElementById('addBannerWali');
        const bannerSuper = document.getElementById('addBannerSuper');

        const fieldClassSiswa = document.getElementById('addFieldClassSiswa');
        const fieldClassWali = document.getElementById('addFieldClassWali');
        const fieldClassSuper = document.getElementById('addFieldClassSuper');

        const selectClassSiswa = document.getElementById('add_class_siswa');
        const selectClassWali = document.getElementById('add_class_wali');
        const inputClassSuper = document.getElementById('add_class_super');

        const fieldNisn = document.getElementById('addFieldNisn');
        const fieldNip = document.getElementById('addFieldNip');
        const labelNip = document.getElementById('labelAddNip');
        const inputNisn = document.getElementById('add_nisn');
        const inputNip = document.getElementById('add_nip');

        const submitBtn = document.getElementById('addSubmitBtn');

        roleInput.value = role;

        const inactiveClass = 'flex items-center justify-center gap-2 py-2 px-3 rounded-lg text-xs font-bold transition-all cursor-pointer text-gray-600 dark:text-zinc-400 hover:text-gray-900 dark:hover:text-white';
        const activeClass = 'flex items-center justify-center gap-2 py-2 px-3 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white dark:bg-zinc-700 text-[#f53003] shadow-sm';

        btnSiswa.className = inactiveClass;
        btnWali.className = inactiveClass;
        btnSuper.className = inactiveClass;

        // Hide all banners
        bannerSiswa.classList.add('hidden');
        bannerWali.classList.add('hidden');
        bannerSuper.classList.add('hidden');

        // Reset class inputs
        fieldClassSiswa.classList.add('hidden');
        selectClassSiswa.disabled = true;
        selectClassSiswa.removeAttribute('required');

        fieldClassWali.classList.add('hidden');
        selectClassWali.disabled = true;
        selectClassWali.removeAttribute('required');

        fieldClassSuper.classList.add('hidden');
        inputClassSuper.disabled = true;
        inputClassSuper.removeAttribute('required');

        // Reset identification
        fieldNisn.classList.add('hidden');
        inputNisn.disabled = true;
        inputNisn.removeAttribute('required');

        fieldNip.classList.add('hidden');
        inputNip.disabled = true;
        inputNip.removeAttribute('required');

        if (role === 'admin') {
            btnWali.className = activeClass;
            bannerWali.classList.remove('hidden');

            fieldClassWali.classList.remove('hidden');
            selectClassWali.disabled = false;
            selectClassWali.setAttribute('required', 'required');

            fieldNip.classList.remove('hidden');
            labelNip.innerText = 'NIP';
            inputNip.disabled = false;
            inputNip.setAttribute('required', 'required');
            inputNip.placeholder = 'Nomor Induk Pegawai (NIP)';

            submitBtn.innerText = 'Daftar Sebagai Wali Kelas';
        } else if (role === 'superadmin') {
            btnSuper.className = activeClass;
            bannerSuper.classList.remove('hidden');

            fieldClassSuper.classList.remove('hidden');
            inputClassSuper.disabled = false;
            inputClassSuper.setAttribute('required', 'required');

            fieldNip.classList.remove('hidden');
            labelNip.innerText = 'NIP (OPSIONAL)';
            inputNip.disabled = false;
            inputNip.removeAttribute('required');
            inputNip.placeholder = 'Nomor Induk Pegawai (opsional)';

            submitBtn.innerText = 'Daftar Sebagai Superadmin';
        } else {
            btnSiswa.className = activeClass;
            bannerSiswa.classList.remove('hidden');

            fieldClassSiswa.classList.remove('hidden');
            selectClassSiswa.disabled = false;
            selectClassSiswa.setAttribute('required', 'required');

            fieldNisn.classList.remove('hidden');
            inputNisn.disabled = false;
            inputNisn.setAttribute('required', 'required');

            submitBtn.innerText = 'Daftar Sebagai Siswa';
        }
    }

    function openAddUserModal() {
        document.getElementById('addUserModal').classList.remove('hidden');
    }

    function closeAddUserModal() {
        document.getElementById('addUserModal').classList.add('hidden');
    }

    function updateEditRoleLabel(role) {
        const label = document.getElementById('edit_id_label');
        const input = document.getElementById('edit_nisn');
        if (role === 'siswa') {
            label.innerText = 'NISN';
            input.placeholder = '10 digit NISN';
        } else if (role === 'admin') {
            label.innerText = 'NIP';
            input.placeholder = 'Nomor Induk Pegawai (NIP)';
        } else {
            label.innerText = 'NIP (OPSIONAL)';
            input.placeholder = 'Nomor Induk Pegawai (opsional)';
        }
    }

    function openEditUserModal(user) {
        const modal = document.getElementById('editUserModal');
        const form = document.getElementById('editUserForm');
        
        // Set Action URL
        form.action = `/users/${user.id}`;
        
        // Set Value ke input
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_username').value = user.username;
        document.getElementById('edit_class').value = user.class;
        document.getElementById('edit_nisn').value = user.role === 'siswa' ? (user.nisn || '') : (user.nip || user.nisn || '');
        
        // Format birth_date ke YYYY-MM-DD
        if (user.birth_date) {
            const date = new Date(user.birth_date);
            const yyyy = date.getFullYear();
            let mm = date.getMonth() + 1;
            let dd = date.getDate();
            if (mm < 10) mm = '0' + mm;
            if (dd < 10) dd = '0' + dd;
            document.getElementById('edit_birth_date').value = `${yyyy}-${mm}-${dd}`;
        } else {
            document.getElementById('edit_birth_date').value = '';
        }
        
        document.getElementById('edit_role').value = user.role;
        updateEditRoleLabel(user.role);
        document.getElementById('edit_password').value = ''; // Kosongkan
        
        modal.classList.remove('hidden');
    }

    function closeEditUserModal() {
        document.getElementById('editUserModal').classList.add('hidden');
    }

    @if ($errors->any() && old('_modal') === 'add')
        document.addEventListener('DOMContentLoaded', function() {
            openAddUserModal();
        });
    @endif
</script>
@endsection
