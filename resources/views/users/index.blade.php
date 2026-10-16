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
        <div class="inline-block align-bottom bg-white dark:bg-zinc-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 dark:border-zinc-800">
            <div class="p-6">
                <div class="flex justify-between items-center pb-4 border-b border-gray-100 dark:border-zinc-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white" id="modal-title">Tambah User Baru</h3>
                    <button onclick="closeAddUserModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form action="{{ route('users.store') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" required class="w-full px-3.5 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Username</label>
                            <input type="text" name="username" required class="w-full px-3.5 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Password</label>
                            <input type="password" name="password" required placeholder="Min. 6 karakter" class="w-full px-3.5 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Kelas</label>
                            <input type="text" name="class" required placeholder="Contoh: XII PPLG 1 / Admin" class="w-full px-3.5 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">NISN / NIP</label>
                            <input type="text" name="nisn" required class="w-full px-3.5 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Tanggal Lahir</label>
                            <input type="date" name="birth_date" required class="w-full px-3.5 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Peran (Role)</label>
                            <select name="role" required class="w-full px-3.5 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all cursor-pointer">
                                <option value="siswa">Siswa</option>
                                <option value="admin">Admin</option>
                                <option value="superadmin">Superadmin</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-zinc-800">
                        <button type="button" onclick="closeAddUserModal()" class="px-4 py-2 border border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-800 rounded-xl text-sm font-semibold text-gray-700 dark:text-zinc-300 transition-all cursor-pointer">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-[#f53003] hover:bg-[#d62700] text-white font-semibold rounded-xl text-sm transition-all cursor-pointer">Simpan</button>
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
        <div class="inline-block align-bottom bg-white dark:bg-zinc-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 dark:border-zinc-800">
            <div class="p-6">
                <div class="flex justify-between items-center pb-4 border-b border-gray-100 dark:border-zinc-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white" id="modal-title">Edit Data User</h3>
                    <button onclick="closeEditUserModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form id="editUserForm" action="" method="POST" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" id="edit_name" required class="w-full px-3.5 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Username</label>
                            <input type="text" name="username" id="edit_username" required class="w-full px-3.5 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all">
                        </div>
                        <div>
                            <div class="flex justify-between">
                                <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Password</label>
                                <span class="text-[10px] text-gray-400">Opsional</span>
                            </div>
                            <input type="password" name="password" id="edit_password" placeholder="Isi hanya untuk ganti" class="w-full px-3.5 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Kelas</label>
                            <input type="text" name="class" id="edit_class" required class="w-full px-3.5 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">NISN / NIP</label>
                            <input type="text" name="nisn" id="edit_nisn" required class="w-full px-3.5 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Tanggal Lahir</label>
                            <input type="date" name="birth_date" id="edit_birth_date" required class="w-full px-3.5 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5">Peran (Role)</label>
                            <select name="role" id="edit_role" required class="w-full px-3.5 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] dark:text-white transition-all cursor-pointer">
                                <option value="siswa">Siswa</option>
                                <option value="admin">Admin</option>
                                <option value="superadmin">Superadmin</option>
                            </select>
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
    function openAddUserModal() {
        document.getElementById('addUserModal').classList.remove('hidden');
    }

    function closeAddUserModal() {
        document.getElementById('addUserModal').classList.add('hidden');
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
        document.getElementById('edit_password').value = ''; // Kosongkan
        
        modal.classList.remove('hidden');
    }

    function closeEditUserModal() {
        document.getElementById('editUserModal').classList.add('hidden');
    }
</script>
@endsection
