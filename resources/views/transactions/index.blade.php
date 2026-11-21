@extends('layouts.app')

@section('title', 'Riwayat Transaksi - Smart Savings')

@section('content')
<!-- Header Section -->
<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <div class="flex flex-wrap items-center gap-3 mb-2">
            <h1 class="text-2xl lg:text-3xl font-bold tracking-tight text-[#1b1b18] dark:text-white">Riwayat Transaksi Masuk</h1>
            @if(auth()->user()->role === 'admin')
                <span class="px-3 py-1 bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400 border border-amber-200 dark:border-amber-800 rounded-full text-xs font-bold flex items-center gap-1.5">
                    <i class="fa-solid fa-chalkboard-user"></i> {{ auth()->user()->assigned_class_label }}
                </span>
            @endif
        </div>
        <p class="text-sm text-[#706f6c] dark:text-zinc-400">
            @if(auth()->user()->role === 'admin')
                Menampilkan daftar setoran otomatis dari siswa <b>{{ auth()->user()->assigned_class_label }}</b>.
            @elseif(auth()->user()->role === 'siswa')
                Menampilkan seluruh riwayat setoran otomatis tabungan Anda.
            @else
                Menampilkan daftar setoran otomatis dari seluruh siswa Kelas 4, 5, dan 6.
            @endif
        </p>
    </div>
    <div class="flex flex-wrap items-center gap-2.5">
        <button type="button" 
                onclick="openExportModal()" 
                class="inline-flex items-center px-4 py-2 border border-emerald-500/30 dark:border-emerald-500/40 rounded-xl text-sm font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition-all shadow-xs cursor-pointer"
                title="Pilih tanggal & unduh data riwayat transaksi sebagai file Excel (.xlsx)">
            <i class="fa-solid fa-file-excel mr-2 text-emerald-600 dark:text-emerald-400"></i> Export XLSX
        </button>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm font-semibold bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700/80 transition-all text-[#1b1b18] dark:text-zinc-200">
            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Dashboard
        </a>
    </div>
</div>

<!-- Form Pencarian -->
<form action="{{ route('transactions.index') }}" method="GET" class="mb-6 bg-white dark:bg-zinc-900 rounded-xl p-5 shadow-[0_1px_3px_0_rgba(0,0,0,0.05)] border border-[#19140010] dark:border-zinc-800 flex flex-wrap gap-4 items-end">
    <div class="flex-1 min-w-[200px]">
        <label for="search" class="block text-xs font-semibold text-[#706f6c] dark:text-zinc-400 uppercase tracking-wider mb-1.5">
            {{ auth()->user()->role === 'admin' ? 'Nama Siswa' : 'Nama atau Kelas' }}
        </label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
            </span>
            <input type="text" name="search" id="search" value="{{ request('search') }}"
                   class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 dark:bg-zinc-800 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] focus:ring-1 focus:ring-[#f53003] transition-all text-[#1b1b18] dark:text-white"
                   placeholder="{{ auth()->user()->role === 'admin' ? 'Cari nama siswa di ' . auth()->user()->assigned_class_label . '...' : 'Cari nama atau kelas...' }}">
        </div>
    </div>

    <div class="w-full md:w-[200px]">
        <label for="date" class="block text-xs font-semibold text-[#706f6c] dark:text-zinc-400 uppercase tracking-wider mb-1.5">Tanggal Masuk</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <i class="fa-solid fa-calendar text-xs"></i>
            </span>
            <input type="date" name="date" id="date" value="{{ request('date') }}"
                   class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 dark:bg-zinc-800 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] focus:ring-1 focus:ring-[#f53003] transition-all text-[#1b1b18] dark:text-white">
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <button type="submit" class="px-5 py-2 bg-[#f53003] hover:bg-[#d62700] text-white font-semibold rounded-xl text-sm transition-all shadow-[0_2px_8px_rgba(245,48,3,0.15)] flex items-center cursor-pointer">
            <i class="fa-solid fa-filter mr-1.5 text-xs"></i> Cari
        </button>
        @if(request()->filled('search') || request()->filled('date'))
            <a href="{{ route('transactions.index') }}" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-700/80 text-gray-700 dark:text-zinc-300 font-semibold rounded-xl text-sm transition-all flex items-center">
                Reset
            </a>
        @endif
        <button type="button" 
                onclick="openExportModal()" 
                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-sm transition-all shadow-[0_2px_8px_rgba(5,150,105,0.2)] flex items-center ml-auto cursor-pointer" 
                title="Pilih tanggal & export data ke XLSX">
            <i class="fa-solid fa-file-excel mr-1.5"></i> Export XLSX
        </button>
    </div>
</form>

<!-- Table Card -->
<div class="bg-white dark:bg-zinc-900 rounded-xl shadow-[0_1px_3px_0_rgba(0,0,0,0.05)] border border-[#19140010] dark:border-zinc-800 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-zinc-800/40 text-[#706f6c] dark:text-zinc-400 text-xs font-semibold uppercase border-b border-gray-100 dark:border-zinc-800">
                    <th class="px-6 py-4">No</th>
                    <th class="px-6 py-4">Tanggal Uang Masuk</th>
                    <th class="px-6 py-4">Nama Siswa</th>
                    <th class="px-6 py-4">Kelas</th>
                    <th class="px-6 py-4">Kategori / Deskripsi</th>
                    <th class="px-6 py-4 text-right">Jumlah Uang Masuk</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#19140010] dark:divide-zinc-800 text-sm">
                @forelse($transactions as $index => $transaction)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="px-6 py-4 text-xs text-[#706f6c] dark:text-zinc-400 font-medium">
                            {{ ($transactions->currentPage() - 1) * $transactions->perPage() + $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-[#706f6c] dark:text-zinc-400">
                            {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-[#1b1b18] dark:text-white">
                            {{ $transaction->user->name ?? 'IoT Guest' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-[#706f6c] dark:text-zinc-400">
                            {{ $transaction->user->class ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-[#1b1b18] dark:text-zinc-300">
                            <span class="font-medium">{{ $transaction->category }}</span>
                            @if($transaction->description)
                                <p class="text-xs text-[#706f6c] dark:text-zinc-400 mt-0.5">{{ $transaction->description }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right font-bold text-emerald-600 dark:text-emerald-400">
                            + Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-[#706f6c] dark:text-zinc-400">
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <i class="fa-solid fa-folder-open text-3xl text-gray-300"></i>
                                <span>
                                    @if(auth()->user()->role === 'admin')
                                        Belum ada transaksi masuk tercatat untuk siswa {{ auth()->user()->assigned_class_label }}.
                                    @else
                                        Belum ada transaksi masuk tercatat di sistem.
                                    @endif
                                </span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Container -->
    @if($transactions->hasPages())
        <div class="px-6 py-4 border-t border-[#19140010] dark:border-zinc-800 bg-gray-50/50 dark:bg-zinc-800/40">
            {{ $transactions->links() }}
        </div>
    @endif
</div>

<!-- Modal Export XLSX Pilihan Tanggal -->
<div id="exportModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-export-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4 text-center">
        <!-- Backdrop -->
        <div onclick="closeExportModal()" class="fixed inset-0 bg-black/50 transition-opacity backdrop-blur-xs" aria-hidden="true"></div>

        <!-- Modal Card -->
        <div class="relative bg-white dark:bg-zinc-900 rounded-3xl p-6 sm:p-8 text-left shadow-2xl transform transition-all w-full max-w-[500px] border border-gray-100 dark:border-zinc-800 z-10">
            <!-- Modal Header -->
            <div class="flex justify-between items-start mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-file-excel"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white" id="modal-export-title">Export Riwayat Transaksi</h3>
                        <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Pilih periode tanggal data yang ingin diunduh (.xlsx)</p>
                    </div>
                </div>
                <button type="button" onclick="closeExportModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Target Scope Badge -->
            <div class="mb-5 p-3 bg-gray-50 dark:bg-zinc-800/60 rounded-xl border border-gray-100 dark:border-zinc-800 flex items-center justify-between text-xs">
                <span class="text-gray-500 dark:text-zinc-400">Hak Akses:</span>
                @if(auth()->user()->role === 'admin')
                    <span class="font-bold text-amber-700 dark:text-amber-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-chalkboard-user"></i> {{ auth()->user()->assigned_class_label }}
                    </span>
                @elseif(auth()->user()->role === 'siswa')
                    <span class="font-bold text-blue-600 dark:text-blue-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-graduation-cap"></i> Tabungan Pribadi
                    </span>
                @else
                    <span class="font-bold text-purple-700 dark:text-purple-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-user-shield"></i> Superadmin (Akses Semua Kelas)
                    </span>
                @endif
            </div>

            <!-- Form Export -->
            <form action="{{ route('transactions.export') }}" method="GET" id="exportForm" onsubmit="setTimeout(closeExportModal, 600)">
                <!-- Preserve current search keyword if any -->
                @if(request()->filled('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <div class="mb-4 p-2.5 bg-blue-50/70 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/30 rounded-xl text-blue-700 dark:text-blue-300 text-xs flex items-center gap-2">
                        <i class="fa-solid fa-filter text-xs"></i>
                        <span>Menyaring kata kunci pencarian: <b>"{{ request('search') }}"</b></span>
                    </div>
                @endif

                <!-- Pilihan Kelas Khusus Superadmin -->
                @if(auth()->user()->role === 'superadmin')
                    <div class="mb-5 p-4 bg-purple-50/60 dark:bg-purple-950/20 border border-purple-100 dark:border-purple-900/30 rounded-2xl">
                        <label for="export_class_filter" class="block text-xs font-bold text-purple-900 dark:text-purple-300 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <i class="fa-solid fa-graduation-cap"></i> Pilih Kelas yang Diexport
                        </label>
                        <div class="relative">
                            <select name="class_filter" id="export_class_filter" 
                                    class="w-full pl-3.5 pr-10 py-2.5 bg-white dark:bg-zinc-800 border border-purple-200 dark:border-purple-800 rounded-xl text-sm focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all appearance-none text-gray-900 dark:text-white font-medium cursor-pointer">
                                <option value="all">Semua Kelas (Kelas 4, 5, dan 6)</option>
                                <option value="4">Kelas 4 Saja</option>
                                <option value="5">Kelas 5 Saja</option>
                                <option value="6">Kelas 6 Saja</option>
                            </select>
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-purple-400">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </span>
                        </div>
                        <p class="text-[11px] text-purple-600/80 dark:text-purple-400/80 mt-1.5">
                            Pilih apakah ingin mengekspor seluruh kelas sekaligus atau membatasi pada satu kelas tertentu.
                        </p>
                    </div>
                @endif

                <label class="block text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider mb-2.5">Pilih Periode Tanggal</label>

                <div class="space-y-2.5 mb-6">
                    <!-- Opsi 1: Semua Data -->
                    <label class="flex items-start gap-3 p-3 rounded-xl border border-gray-200 dark:border-zinc-700 hover:border-emerald-500 dark:hover:border-emerald-500 cursor-pointer transition-all bg-white dark:bg-zinc-900">
                        <input type="radio" name="export_mode" value="all" id="mode_all" {{ request()->filled('date') ? '' : 'checked' }} onchange="toggleDateInputs('all')" class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white block">Semua Tanggal</span>
                            <span class="text-xs text-gray-500 dark:text-zinc-400">Unduh seluruh riwayat transaksi yang tercatat</span>
                        </div>
                    </label>

                    <!-- Opsi 2: Hari Ini Saja -->
                    <label class="flex items-start gap-3 p-3 rounded-xl border border-gray-200 dark:border-zinc-700 hover:border-emerald-500 dark:hover:border-emerald-500 cursor-pointer transition-all bg-white dark:bg-zinc-900">
                        <input type="radio" name="export_mode" value="today" id="mode_today" onchange="toggleDateInputs('today')" class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white block">Hari Ini</span>
                            <span class="text-xs text-gray-500 dark:text-zinc-400">Transaksi khusus tanggal hari ini ({{ \Carbon\Carbon::today()->format('d M Y') }})</span>
                        </div>
                    </label>

                    <!-- Opsi 3: Satu Tanggal Tertentu -->
                    <div class="rounded-xl border border-gray-200 dark:border-zinc-700 hover:border-emerald-500 dark:hover:border-emerald-500 p-3 bg-white dark:bg-zinc-900 transition-all">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="radio" name="export_mode" value="single" id="mode_single" {{ request()->filled('date') ? 'checked' : '' }} onchange="toggleDateInputs('single')" class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white block">Pilih Satu Tanggal Tertentu</span>
                                <span class="text-xs text-gray-500 dark:text-zinc-400">Unduh transaksi hanya pada 1 tanggal spesifik</span>
                            </div>
                        </label>
                        <div id="singleDateContainer" class="{{ request()->filled('date') ? '' : 'hidden' }} mt-3 pt-3 border-t border-gray-100 dark:border-zinc-800 pl-7">
                            <label for="export_single_date" class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1">Tanggal Transaksi:</label>
                            <input type="date" name="date" id="export_single_date" value="{{ request('date') ?: date('Y-m-d') }}" {{ request()->filled('date') ? '' : 'disabled' }}
                                   class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-gray-900 dark:text-white">
                        </div>
                    </div>

                    <!-- Opsi 4: Rentang Tanggal -->
                    <div class="rounded-xl border border-gray-200 dark:border-zinc-700 hover:border-emerald-500 dark:hover:border-emerald-500 p-3 bg-white dark:bg-zinc-900 transition-all">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="radio" name="export_mode" value="range" id="mode_range" onchange="toggleDateInputs('range')" class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white block">Rentang Tanggal (Custom Range)</span>
                                <span class="text-xs text-gray-500 dark:text-zinc-400">Tentukan periode tanggal mulai s/d tanggal selesai</span>
                            </div>
                        </label>
                        <div id="rangeDateContainer" class="hidden mt-3 pt-3 border-t border-gray-100 dark:border-zinc-800 pl-7 grid grid-cols-2 gap-3">
                            <div>
                                <label for="export_start_date" class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1">Dari Tanggal:</label>
                                <input type="date" name="start_date" id="export_start_date" value="{{ date('Y-m-01') }}" disabled
                                       class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label for="export_end_date" class="block text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1">Sampai Tanggal:</label>
                                <input type="date" name="end_date" id="export_end_date" value="{{ date('Y-m-d') }}" disabled
                                       class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-gray-900 dark:text-white">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100 dark:border-zinc-800">
                    <button type="button" onclick="closeExportModal()" class="px-4 py-2.5 text-sm font-semibold text-gray-600 dark:text-zinc-400 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-xl transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-md transition-all flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-download"></i> Unduh File Excel (.xlsx)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openExportModal() {
        document.getElementById('exportModal').classList.remove('hidden');
    }

    function closeExportModal() {
        document.getElementById('exportModal').classList.add('hidden');
    }

    function toggleDateInputs(mode) {
        const singleContainer = document.getElementById('singleDateContainer');
        const rangeContainer = document.getElementById('rangeDateContainer');
        const singleInput = document.getElementById('export_single_date');
        const startInput = document.getElementById('export_start_date');
        const endInput = document.getElementById('export_end_date');

        if (mode === 'single') {
            singleContainer.classList.remove('hidden');
            rangeContainer.classList.add('hidden');
            singleInput.disabled = false;
            startInput.disabled = true;
            endInput.disabled = true;
        } else if (mode === 'range') {
            singleContainer.classList.add('hidden');
            rangeContainer.classList.remove('hidden');
            singleInput.disabled = true;
            startInput.disabled = false;
            endInput.disabled = false;
        } else {
            singleContainer.classList.add('hidden');
            rangeContainer.classList.add('hidden');
            singleInput.disabled = true;
            startInput.disabled = true;
            endInput.disabled = true;
        }
    }
</script>
@endsection
