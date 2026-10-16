@extends('layouts.app')

@section('title', 'Dashboard - Smart Savings')

@section('content')
<!-- Header Section -->
<div class="mb-8">
    <h1 class="text-2xl lg:text-3xl font-bold tracking-tight mb-2 text-[#1b1b18] dark:text-white">Dashboard IoT Smart Savings</h1>
    <p class="text-sm text-[#706f6c] dark:text-zinc-400">Pantau aktivitas tabungan siswa SD Kelas 4, 5, dan 6 secara real-time.</p>
</div>

<!-- Metrics Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card 1: Total Tabungan -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl p-6 shadow-[0_1px_3px_0_rgba(0,0,0,0.05)] border border-[#19140010] dark:border-zinc-800">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-xs font-semibold text-[#706f6c] dark:text-zinc-400 uppercase tracking-wider">Total Tabungan</p>
                <h3 class="text-2xl font-bold mt-1 text-emerald-600 dark:text-emerald-400">Rp {{ number_format($totalSaved, 0, ',', '.') }}</h3>
            </div>
            <div class="bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 p-2.5 rounded-lg">
                <i class="fa-solid fa-piggy-bank text-lg"></i>
            </div>
        </div>
        <div class="text-xs text-[#706f6c] dark:text-zinc-400">
            <span>Akumulasi tabungan semua siswa</span>
        </div>
    </div>

    <!-- Card 2: Pemasukan Hari Ini -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl p-6 shadow-[0_1px_3px_0_rgba(0,0,0,0.05)] border border-[#19140010] dark:border-zinc-800">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-xs font-semibold text-[#706f6c] dark:text-zinc-400 uppercase tracking-wider">Pemasukan Hari Ini</p>
                <h3 class="text-2xl font-bold mt-1 text-blue-600 dark:text-blue-400">Rp {{ number_format($dailyIncome, 0, ',', '.') }}</h3>
            </div>
            <div class="bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 p-2.5 rounded-lg">
                <i class="fa-solid fa-calendar-day text-lg"></i>
            </div>
        </div>
        <div class="text-xs text-[#706f6c] dark:text-zinc-400">
            <span>Total uang masuk hari ini</span>
        </div>
    </div>

    <!-- Card 3: Pemasukan Minggu Ini -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl p-6 shadow-[0_1px_3px_0_rgba(0,0,0,0.05)] border border-[#19140010] dark:border-zinc-800">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-xs font-semibold text-[#706f6c] dark:text-zinc-400 uppercase tracking-wider">Pemasukan Minggu Ini</p>
                <h3 class="text-2xl font-bold mt-1 text-purple-600 dark:text-purple-400">Rp {{ number_format($weeklyIncome, 0, ',', '.') }}</h3>
            </div>
            <div class="bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 p-2.5 rounded-lg">
                <i class="fa-solid fa-calendar-week text-lg"></i>
            </div>
        </div>
        <div class="text-xs text-[#706f6c] dark:text-zinc-400">
            <span>Total uang masuk 7 hari terakhir</span>
        </div>
    </div>

    <!-- Card 4: Total Siswa Menabung -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl p-6 shadow-[0_1px_3px_0_rgba(0,0,0,0.05)] border border-[#19140010] dark:border-zinc-800">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-xs font-semibold text-[#706f6c] dark:text-zinc-400 uppercase tracking-wider">Siswa yang Menabung</p>
                <h3 class="text-2xl font-bold mt-1 text-[#f53003] dark:text-rose-400">{{ $totalStudentsSaving }} Siswa</h3>
            </div>
            <div class="bg-rose-50 dark:bg-rose-950/40 text-[#f53003] dark:text-rose-400 p-2.5 rounded-lg">
                <i class="fa-solid fa-users text-lg"></i>
            </div>
        </div>
        <div class="text-xs text-[#706f6c] dark:text-zinc-400">
            <span>Jumlah siswa terdaftar yang aktif menabung</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left 2 Columns: Savings Goals & Transactions -->
    <div class="lg:col-span-2 space-y-8">
        
        <!-- Recent Transactions Section -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-[0_1px_3px_0_rgba(0,0,0,0.05)] border border-[#19140010] dark:border-zinc-800 overflow-hidden">
            <div class="px-6 py-5 border-b border-[#19140015] dark:border-zinc-800 bg-white dark:bg-zinc-900 flex justify-between items-center">
                <h3 class="font-bold text-lg text-[#1b1b18] dark:text-white">Riwayat Transaksi Masuk Mesin</h3>
                <a href="{{ route('transactions.index') }}" class="text-sm font-semibold text-[#059669] dark:text-emerald-400 hover:underline">
                    Lihat Selengkapnya &rarr;
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-zinc-800/40 text-[#706f6c] dark:text-zinc-400 text-xs font-semibold uppercase border-b border-gray-100 dark:border-zinc-800">
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Nama Siswa</th>
                            <th class="px-6 py-4">Kelas</th>
                            <th class="px-6 py-4">Kategori / Deskripsi</th>
                            <th class="px-6 py-4 text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#19140010] dark:divide-zinc-800 text-sm">
                        @forelse($recentTransactions as $transaction)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/30 transition-colors">
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
                                <td colspan="5" class="px-6 py-8 text-center text-[#706f6c] dark:text-zinc-400">Belum ada setoran masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Sidebar Class Summary widget -->
    <div class="space-y-8">
        
        <!-- Class Savings Summary Widget -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-[0_1px_3px_0_rgba(0,0,0,0.05)] border border-[#19140010] dark:border-zinc-800 p-6">
            <h3 class="font-bold text-lg mb-1 text-[#1b1b18] dark:text-white">Pemasukan Hari Ini</h3>
            <p class="text-xs text-[#706f6c] dark:text-zinc-400 mb-6">Ringkasan tabungan masuk siswa Kelas 4, 5, dan 6</p>
            
            <div class="space-y-5">
                <!-- Kelas 4 -->
                <div class="p-4 bg-emerald-50/40 dark:bg-emerald-950/10 border border-emerald-100/50 dark:border-emerald-900/30 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-emerald-600 text-white w-9 h-9 rounded-xl flex items-center justify-center font-bold">4</div>
                        <div>
                            <h4 class="font-bold text-sm text-[#1b1b18] dark:text-white">Kelas 4</h4>
                            <p class="text-xs text-[#706f6c] dark:text-zinc-400">Setoran masuk hari ini</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-bold text-emerald-600 dark:text-emerald-400 text-sm">Rp {{ number_format($class4Daily, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Kelas 5 -->
                <div class="p-4 bg-blue-50/40 dark:bg-blue-950/10 border border-blue-100/50 dark:border-blue-900/30 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-600 text-white w-9 h-9 rounded-xl flex items-center justify-center font-bold">5</div>
                        <div>
                            <h4 class="font-bold text-sm text-[#1b1b18] dark:text-white">Kelas 5</h4>
                            <p class="text-xs text-[#706f6c] dark:text-zinc-400">Setoran masuk hari ini</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-bold text-blue-600 dark:text-blue-400 text-sm">Rp {{ number_format($class5Daily, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Kelas 6 -->
                <div class="p-4 bg-purple-50/40 dark:bg-purple-950/10 border border-purple-100/50 dark:border-purple-900/30 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-purple-600 text-white w-9 h-9 rounded-xl flex items-center justify-center font-bold">6</div>
                        <div>
                            <h4 class="font-bold text-sm text-[#1b1b18] dark:text-white">Kelas 6</h4>
                            <p class="text-xs text-[#706f6c] dark:text-zinc-400">Setoran masuk hari ini</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-bold text-purple-600 dark:text-purple-400 text-sm">Rp {{ number_format($class6Daily, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
