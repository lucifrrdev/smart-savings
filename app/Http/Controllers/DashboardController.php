<?php

namespace App\Http\Controllers;

use App\Models\SavingsGoal;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $goals = SavingsGoal::with('transactions')->orderBy('target_date')->get();

        $activeGoals = $goals->where('status', 'active');
        $achievedGoals = $goals->where('status', 'achieved');
        $pausedGoals = $goals->where('status', 'paused');

        $totalTarget = $goals->sum('target_amount');
        $totalSaved = $goals->sum('current_amount');
        $overallProgress = $totalTarget > 0 ? round(($totalSaved / $totalTarget) * 100, 1) : 0;

        $incomeThisMonth = Transaction::where('type', 'income')
            ->whereMonth('transaction_date', Carbon::now()->month)
            ->whereYear('transaction_date', Carbon::now()->year)
            ->sum('amount');

        $expenseThisMonth = Transaction::where('type', 'expense')
            ->whereMonth('transaction_date', Carbon::now()->month)
            ->whereYear('transaction_date', Carbon::now()->year)
            ->sum('amount');

        $depositThisMonth = Transaction::whereIn('type', ['deposit'])
            ->whereMonth('transaction_date', Carbon::now()->month)
            ->whereYear('transaction_date', Carbon::now()->year)
            ->sum('amount');

        $withdrawalThisMonth = Transaction::whereIn('type', ['withdrawal'])
            ->whereMonth('transaction_date', Carbon::now()->month)
            ->whereYear('transaction_date', Carbon::now()->year)
            ->sum('amount');

        $recentTransactions = Transaction::with('savingsGoal')
            ->latest('transaction_date')
            ->limit(10)
            ->get();

        $monthlyData = $this->getMonthlyChartData();
        $categoryData = $this->getCategoryChartData();
        $goalProgressData = $this->getGoalProgressData($activeGoals);

        return view('dashboard', compact(
            'goals',
            'activeGoals',
            'achievedGoals',
            'pausedGoals',
            'totalTarget',
            'totalSaved',
            'overallProgress',
            'incomeThisMonth',
            'expenseThisMonth',
            'depositThisMonth',
            'withdrawalThisMonth',
            'recentTransactions',
            'monthlyData',
            'categoryData',
            'goalProgressData'
        ));
    }

    private function getMonthlyChartData()
    {
        $months = [];
        $incomeData = [];
        $expenseData = [];
        $depositData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');

            $income = Transaction::where('type', 'income')
                ->whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->sum('amount');

            $expense = Transaction::where('type', 'expense')
                ->whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->sum('amount');

            $deposit = Transaction::where('type', 'deposit')
                ->whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->sum('amount');

            $incomeData[] = (float) $income;
            $expenseData[] = (float) $expense;
            $depositData[] = (float) $deposit;
        }

        return [
            'labels' => $months,
            'income' => $incomeData,
            'expense' => $expenseData,
            'deposit' => $depositData,
        ];
    }

    private function getCategoryChartData()
    {
        $expenses = Transaction::where('type', 'expense')
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        return [
            'labels' => $expenses->pluck('category')->toArray(),
            'data' => $expenses->pluck('total')->map(fn($v) => (float) $v)->toArray(),
        ];
    }

    private function getGoalProgressData($activeGoals)
    {
        return [
            'labels' => $activeGoals->pluck('title')->toArray(),
            'current' => $activeGoals->pluck('current_amount')->map(fn($v) => (float) $v)->toArray(),
            'target' => $activeGoals->pluck('target_amount')->map(fn($v) => (float) $v)->toArray(),
        ];
    }
}