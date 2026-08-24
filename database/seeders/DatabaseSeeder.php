<?php

namespace Database\Seeders;

use App\Models\SavingsGoal;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        SavingsGoal::truncate();
        Transaction::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $goals = [
            [
                'title' => 'Dana Darurat',
                'target_amount' => 10000000,
                'current_amount' => 3500000,
                'target_date' => Carbon::today()->addMonths(6),
                'category' => 'Keamanan',
                'color' => '#ef4444',
                'icon' => 'fa-shield-alt',
                'description' => 'Dana cadangan untuk kebutuhan mendadak seperti biaya medis atau perbaikan rumah.',
                'status' => 'active',
            ],
            [
                'title' => 'Liburan ke Bali',
                'target_amount' => 5000000,
                'current_amount' => 1200000,
                'target_date' => Carbon::today()->addMonths(4),
                'category' => 'Hiburan',
                'color' => '#f97316',
                'icon' => 'fa-umbrella-beach',
                'description' => 'Tabungan untuk liburan 5 hari 4 malam ke Bali akhir tahun ini.',
                'status' => 'active',
            ],
            [
                'title' => 'Laptop Baru',
                'target_amount' => 15000000,
                'current_amount' => 15000000,
                'target_date' => Carbon::today()->subDays(5),
                'category' => 'Teknologi',
                'color' => '#3b82f6',
                'icon' => 'fa-laptop',
                'description' => 'MacBook Air M2 untuk keperluan kerja dan belajar.',
                'status' => 'achieved',
            ],
            [
                'title' => 'DP Motor',
                'target_amount' => 20000000,
                'current_amount' => 8000000,
                'target_date' => Carbon::today()->addMonths(8),
                'category' => 'Kendaraan',
                'color' => '#8b5cf6',
                'icon' => 'fa-motorcycle',
                'description' => 'Down payment motor baru untuk kommuting sehari-hari.',
                'status' => 'active',
            ],
            [
                'title' => 'Kursus Bahasa Inggris',
                'target_amount' => 3000000,
                'current_amount' => 500000,
                'target_date' => Carbon::today()->addMonths(3),
                'category' => 'Pendidikan',
                'color' => '#10b981',
                'icon' => 'fa-graduation-cap',
                'description' => 'Biaya kursus bahasa Inggris intensif 3 bulan.',
                'status' => 'paused',
            ],
        ];

        foreach ($goals as $goalData) {
            $goal = SavingsGoal::create($goalData);

            if ($goal->status === 'active' || $goal->status === 'achieved') {
                $this->createTransactionsForGoal($goal);
            }
        }

        $this->createGeneralTransactions();
    }

    private function createTransactionsForGoal(SavingsGoal $goal): void
    {
        $startDate = Carbon::today()->subMonths(3);
        $monthlyDeposit = $goal->target_amount / 6;

        for ($i = 0; $i < 3; $i++) {
            $date = $startDate->copy()->addMonths($i)->day(15);
            $amount = $monthlyDeposit + (rand(-200000, 200000));

            if ($goal->current_amount >= $amount) {
                Transaction::create([
                    'savings_goal_id' => $goal->id,
                    'type' => 'deposit',
                    'amount' => $amount,
                    'category' => $goal->category,
                    'description' => "Setoran bulanan {$date->format('F Y')} untuk {$goal->title}",
                    'transaction_date' => $date,
                ]);
            }
        }

        if ($goal->status === 'achieved') {
            Transaction::create([
                'savings_goal_id' => $goal->id,
                'type' => 'withdrawal',
                'amount' => $goal->target_amount,
                'category' => $goal->category,
                'description' => "Penarikan dana untuk pembelian {$goal->title}",
                'transaction_date' => $goal->target_date,
            ]);
        }
    }

    private function createGeneralTransactions(): void
    {
        $incomeCategories = [
            ['category' => 'Gaji', 'amount' => 8000000, 'description' => 'Gaji bulanan'],
            ['category' => 'Freelance', 'amount' => 1500000, 'description' => 'Project desain website'],
            ['category' => 'Bonus', 'amount' => 2000000, 'description' => 'Bonus kinerja triwulan'],
        ];

        $expenseCategories = [
            ['category' => 'Makanan', 'amount' => 2500000, 'description' => 'Belanja bulanan & makan di luar'],
            ['category' => 'Transport', 'amount' => 800000, 'description' => 'Bensin & ojek online'],
            ['category' => 'Tagihan', 'amount' => 1200000, 'description' => 'Listrik, air, internet, pulsa'],
            ['category' => 'Hiburan', 'amount' => 600000, 'description' => 'Streaming, game, nonton bioskop'],
            ['category' => 'Kesehatan', 'amount' => 400000, 'description' => 'Vitamin & cek kesehatan rutin'],
            ['category' => 'Belanja', 'amount' => 900000, 'description' => 'Perawatan diri & kebutuhan rumah'],
        ];

        foreach ($incomeCategories as $inc) {
            for ($i = 0; $i < 3; $i++) {
                $date = Carbon::today()->subMonths(2 - $i)->day(rand(1, 5));
                Transaction::create([
                    'savings_goal_id' => null,
                    'type' => 'income',
                    'amount' => $inc['amount'] + rand(-200000, 200000),
                    'category' => $inc['category'],
                    'description' => $inc['description'],
                    'transaction_date' => $date,
                ]);
            }
        }

        foreach ($expenseCategories as $exp) {
            for ($i = 0; $i < 3; $i++) {
                $date = Carbon::today()->subMonths(2 - $i)->day(rand(5, 25));
                Transaction::create([
                    'savings_goal_id' => null,
                    'type' => 'expense',
                    'amount' => $exp['amount'] + rand(-100000, 100000),
                    'category' => $exp['category'],
                    'description' => $exp['description'],
                    'transaction_date' => $date,
                ]);
            }
        }
    }
}