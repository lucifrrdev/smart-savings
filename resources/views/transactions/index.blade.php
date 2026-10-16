@extends('layouts.app')

@section('title', 'Riwayat Transaksi - Smart Savings')

@section('content')
<!-- Header Section -->
<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl lg:text-3xl font-bold tracking-tight mb-2 text-[#1b1b18] dark:text-white">Riwayat Transaksi Masuk</h1>
        <p class="text-sm text-[#706f6c] dark:text-zinc-400">Menampilkan daftar setoran otomatis dari seluruh siswa Kelas 4, 5, dan 6.</p>
    </div>
    <div>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-200 dark:border-zinc-700 rounded-xl text-sm font-semibold bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700/80 transition-all text-[#1b1b18] dark:text-zinc-200">
            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Dashboard
        </a>
    </div>
</div>

<!-- Form Pencarian -->
<form action="{{ route('transactions.index') }}" method="GET" class="mb-6 bg-white dark:bg-zinc-900 rounded-xl p-5 shadow-[0_1px_3px_0_rgba(0,0,0,0.05)] border border-[#19140010] dark:border-zinc-800 flex flex-wrap gap-4 items-end">
    <div class="flex-1 min-w-[200px]">
        <label for="search" class="block text-xs font-semibold text-[#706f6c] dark:text-zinc-400 uppercase tracking-wider mb-1.5">Nama atau Kelas</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
            </span>
            <input type="text" name="search" id="search" value="{{ request('search') }}"
                   class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 dark:bg-zinc-800 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:border-[#f53003] focus:ring-1 focus:ring-[#f53003] transition-all text-[#1b1b18] dark:text-white"
                   placeholder="Cari nama atau kelas...">
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

    <div class="flex gap-2">
        <button type="submit" class="px-5 py-2 bg-[#f53003] hover:bg-[#d62700] text-white font-semibold rounded-xl text-sm transition-all shadow-[0_2px_8px_rgba(245,48,3,0.15)] flex items-center">
            <i class="fa-solid fa-filter mr-1.5 text-xs"></i> Cari
        </button>
        @if(request()->filled('search') || request()->filled('date'))
            <a href="{{ route('transactions.index') }}" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-700/80 text-gray-700 dark:text-zinc-300 font-semibold rounded-xl text-sm transition-all flex items-center">
                Reset
            </a>
        @endif
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
                                <span>Belum ada transaksi masuk tercatat di sistem.</span>
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
@endsection
