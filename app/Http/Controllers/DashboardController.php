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
        $isAdmin = $user && $user->role === 'admin';
        $managedGrade = $isAdmin ? $user->getAssignedClassGrade() : null;

        // Scope filter helper untuk membatasi query berdasarkan role
        $applyScope = function ($query) use ($user, $isSiswa, $isAdmin, $managedGrade) {
            if ($isSiswa) {
                $query->where('user_id', $user->id);
            } elseif ($isAdmin) {
                $query->whereHas('user', function ($q) use ($managedGrade, $user) {
                    if ($managedGrade) {
                        $q->where(function ($sub) use ($managedGrade, $user) {
                            $sub->where('class', 'like', "%{$managedGrade}%")
                                ->orWhere('class', 'like', "%{$user->class}%");
                        });
                    } else {
                        $q->where('class', 'like', "%{$user->class}%");
                    }
                });
            }
            return $query;
        };

        // 1. Total Tabungan (Semua jenis setoran/deposit sesuai cakupan kelas/user)
        $totalSaved = $applyScope(Transaction::where('type', 'deposit'))->sum('amount');

        // 2. Pemasukan Hari Ini (Setoran uang masuk pada tanggal hari ini)
        $dailyIncome = $applyScope(
            Transaction::where('type', 'deposit')->whereDate('transaction_date', Carbon::today())
        )->sum('amount');

        // 3. Pemasukan Minggu Ini (Setoran uang masuk 7 hari terakhir)
        $weeklyIncome = $applyScope(
            Transaction::where('type', 'deposit')->whereDate('transaction_date', '>=', Carbon::today()->subDays(6))
        )->sum('amount');

        // 4. Total Siswa yang Menabung (Jumlah user unik yang melakukan deposit)
        $totalStudentsSaving = $applyScope(
            Transaction::where('type', 'deposit')
        )->distinct('user_id')->count('user_id');

        // 5. Transaksi Terbaru
        $recentTransactions = $applyScope(
            Transaction::with(['savingsGoal', 'user'])
                ->where('type', 'deposit')
                ->latest('transaction_date')
                ->latest('id')
        )->limit(5)->get();

        // 6. Ringkasan Uang Masuk Hari Ini per Kelas (Sidebar)
        // Definisi konfigurasi tampilan kelas
        $classesConfig = [
            '4' => [
                'name' => 'Kelas 4',
                'grade' => '4',
                'bg_badge' => 'bg-emerald-600',
                'text_color' => 'text-emerald-600 dark:text-emerald-400',
                'box_bg' => 'bg-emerald-50/40 dark:bg-emerald-950/10 border-emerald-100/50 dark:border-emerald-900/30',
            ],
            '5' => [
                'name' => 'Kelas 5',
                'grade' => '5',
                'bg_badge' => 'bg-blue-600',
                'text_color' => 'text-blue-600 dark:text-blue-400',
                'box_bg' => 'bg-blue-50/40 dark:bg-blue-950/10 border-blue-100/50 dark:border-blue-900/30',
            ],
            '6' => [
                'name' => 'Kelas 6',
                'grade' => '6',
                'bg_badge' => 'bg-purple-600',
                'text_color' => 'text-purple-600 dark:text-purple-400',
                'box_bg' => 'bg-purple-50/40 dark:bg-purple-950/10 border-purple-100/50 dark:border-purple-900/30',
            ],
        ];

        // Tentukan kelas yang ditampilkan:
        // Jika Admin (Wali Kelas): hanya kelas yang diampunya
        // Jika Siswa: hanya kelas siswa tersebut
        // Jika Superadmin: semua kelas (4, 5, 6)
        $activeGrades = array_keys($classesConfig);
        if ($isAdmin && $managedGrade) {
            $activeGrades = in_array($managedGrade, ['4', '5', '6']) ? [$managedGrade] : [$managedGrade];
        } elseif ($isSiswa) {
            $siswaGrade = $user->getAssignedClassGrade();
            $activeGrades = ($siswaGrade && in_array($siswaGrade, ['4', '5', '6'])) ? [$siswaGrade] : array_keys($classesConfig);
        }

        $classSummaries = [];
        foreach ($activeGrades as $grade) {
            $cfg = $classesConfig[$grade] ?? [
                'name' => 'Kelas ' . $grade,
                'grade' => $grade,
                'bg_badge' => 'bg-emerald-600',
                'text_color' => 'text-emerald-600 dark:text-emerald-400',
                'box_bg' => 'bg-emerald-50/40 dark:bg-emerald-950/10 border-emerald-100/50 dark:border-emerald-900/30',
            ];

            $dailyQuery = Transaction::where('type', 'deposit')
                ->whereDate('transaction_date', Carbon::today())
                ->whereHas('user', function ($q) use ($grade) {
                    $q->where('class', 'like', "%{$grade}%");
                });

            if ($isSiswa) {
                $dailyQuery->where('user_id', $user->id);
            }

            $cfg['daily_income'] = $dailyQuery->sum('amount');
            $classSummaries[$grade] = $cfg;
        }

        // Variabel kompatibilitas
        $class4Daily = $classSummaries['4']['daily_income'] ?? 0;
        $class5Daily = $classSummaries['5']['daily_income'] ?? 0;
        $class6Daily = $classSummaries['6']['daily_income'] ?? 0;

        return view('dashboard', compact(
            'totalSaved',
            'dailyIncome',
            'weeklyIncome',
            'totalStudentsSaving',
            'class4Daily',
            'class5Daily',
            'class6Daily',
            'classSummaries',
            'recentTransactions'
        ));
    }
}