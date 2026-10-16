<?php

namespace App\Http\Controllers;

use App\Models\SavingsGoal;
use App\Models\Transaction;
use App\Models\TransactionHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isSiswa = $user && $user->role === 'siswa';

        // 1. Total Tabungan (Semua jenis setoran/deposit)
        $totalSavedQuery = Transaction::where('type', 'deposit');
        if ($isSiswa) {
            $totalSavedQuery->where('user_id', $user->id);
        }
        $totalSaved = $totalSavedQuery->sum('amount');

        // 2. Pemasukan Hari Ini (Setoran uang masuk pada tanggal hari ini)
        $dailyIncomeQuery = Transaction::where('type', 'deposit')
            ->whereDate('transaction_date', Carbon::today());
        if ($isSiswa) {
            $dailyIncomeQuery->where('user_id', $user->id);
        }
        $dailyIncome = $dailyIncomeQuery->sum('amount');

        // 3. Pemasukan Minggu Ini (Setoran uang masuk 7 hari terakhir)
        $weeklyIncomeQuery = Transaction::where('type', 'deposit')
            ->whereDate('transaction_date', '>=', Carbon::today()->subDays(6));
        if ($isSiswa) {
            $weeklyIncomeQuery->where('user_id', $user->id);
        }
        $weeklyIncome = $weeklyIncomeQuery->sum('amount');

        // 4. Total Siswa yang Menabung (Jumlah user unik yang melakukan deposit)
        $totalStudentsSavingQuery = Transaction::where('type', 'deposit');
        if ($isSiswa) {
            $totalStudentsSavingQuery->where('user_id', $user->id);
        }
        $totalStudentsSaving = $totalStudentsSavingQuery->distinct('user_id')->count('user_id');

        // 5. Ringkasan Uang Masuk Hari Ini per Kelas (Kelas 4, 5, dan 6)
        $class4DailyQuery = Transaction::where('type', 'deposit')
            ->whereDate('transaction_date', Carbon::today())
            ->whereHas('user', function ($query) {
                $query->where('class', 'like', '%4%');
            });
        if ($isSiswa) {
            $class4DailyQuery->where('user_id', $user->id);
        }
        $class4Daily = $class4DailyQuery->sum('amount');

        $class5DailyQuery = Transaction::where('type', 'deposit')
            ->whereDate('transaction_date', Carbon::today())
            ->whereHas('user', function ($query) {
                $query->where('class', 'like', '%5%');
            });
        if ($isSiswa) {
            $class5DailyQuery->where('user_id', $user->id);
        }
        $class5Daily = $class5DailyQuery->sum('amount');

        $class6DailyQuery = Transaction::where('type', 'deposit')
            ->whereDate('transaction_date', Carbon::today())
            ->whereHas('user', function ($query) {
                $query->where('class', 'like', '%6%');
            });
        if ($isSiswa) {
            $class6DailyQuery->where('user_id', $user->id);
        }
        $class6Daily = $class6DailyQuery->sum('amount');

        // Transaksi Terbaru
        $recentTransactionsQuery = Transaction::with(['savingsGoal', 'user'])
            ->where('type', 'deposit')
            ->latest('transaction_date')
            ->latest('id')
            ->limit(5);
        if ($isSiswa) {
            $recentTransactionsQuery->where('user_id', $user->id);
        }
        $recentTransactions = $recentTransactionsQuery->get();

        return view('dashboard', compact(
            'totalSaved',
            'dailyIncome',
            'weeklyIncome',
            'totalStudentsSaving',
            'class4Daily',
            'class5Daily',
            'class6Daily',
            'recentTransactions'
        ));
    }
}