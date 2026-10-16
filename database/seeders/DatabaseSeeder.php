<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        User::truncate();
        SavingsGoal::truncate();
        Transaction::truncate();

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // 1. Create Users (Siswa Kelas 4, 5, 6)
        $user4 = User::create([
            'name' => 'Budi Utomo',
            'username' => 'budi',
            'class' => 'Kelas 4 A',
            'nisn' => '1111111111',
            'birth_date' => '2016-05-12',
            'password' => 'password',
        ]);

        $user5 = User::create([
            'name' => 'Ani Lestari',
            'username' => 'ani',
            'class' => 'Kelas 5 B',
            'nisn' => '2222222222',
            'birth_date' => '2015-08-20',
            'password' => 'password',
        ]);

        $user6 = User::create([
            'name' => 'Cici Paramida',
            'username' => 'cici',
            'class' => 'Kelas 6 C',
            'nisn' => '3333333333',
            'birth_date' => '2014-11-03',
            'password' => 'password',
        ]);

        // Default admin user for login demo
        User::create([
            'name' => 'Admin Guru',
            'username' => 'admin',
            'class' => 'Wali Kelas 4',
            'nip' => '198501012010011001',
            'birth_date' => '1990-01-01',
            'role' => 'admin',
            'password' => 'admin123',
        ]);

        // Superadmin user
        User::create([
            'name' => 'Super Administrator',
            'username' => 'superadmin',
            'class' => 'IT Support',
            'nisn' => '9999999999',
            'birth_date' => '1985-01-01',
            'role' => 'superadmin',
            'password' => 'password',
        ]);

        // 2. Create Savings Goals associated to users
        $goals = [
            [
                'user_id' => $user4->id,
                'title' => 'Sepeda Baru Budi',
                'target_amount' => 1500000,
                'current_amount' => 500000,
                'target_date' => Carbon::today()->addMonths(6),
                'category' => 'Peralatan',
                'color' => '#ef4444',
                'icon' => 'fa-bicycle',
                'description' => 'Target tabungan Budi untuk beli sepeda baru.',
                'status' => 'active',
            ],
            [
                'user_id' => $user5->id,
                'title' => 'Buku Cerita Ani',
                'target_amount' => 300000,
                'current_amount' => 150000,
                'target_date' => Carbon::today()->addMonths(2),
                'category' => 'Pendidikan',
                'color' => '#10b981',
                'icon' => 'fa-book',
                'description' => 'Target tabungan Ani untuk beli ensiklopedia.',
                'status' => 'active',
            ],
            [
                'user_id' => $user6->id,
                'title' => 'Studi Banding Cici',
                'target_amount' => 1000000,
                'current_amount' => 1000000,
                'target_date' => Carbon::today()->subDays(5),
                'category' => 'Umum',
                'color' => '#3b82f6',
                'icon' => 'fa-campground',
                'description' => 'Dana studi banding akhir tahun.',
                'status' => 'achieved',
            ],
        ];

        foreach ($goals as $goalData) {
            $goal = SavingsGoal::create($goalData);

            // Seed historical transactions for these goals
            if ($goal->status === 'active') {
                // Historic deposits
                Transaction::create([
                    'savings_goal_id' => $goal->id,
                    'user_id' => $goal->user_id,
                    'type' => 'deposit',
                    'amount' => $goal->current_amount,
                    'category' => 'Tabungan',
                    'description' => 'Setoran kumulatif awal',
                    'transaction_date' => Carbon::today()->subDays(10),
                ]);
            } elseif ($goal->status === 'achieved') {
                Transaction::create([
                    'savings_goal_id' => $goal->id,
                    'user_id' => $goal->user_id,
                    'type' => 'deposit',
                    'amount' => $goal->target_amount,
                    'category' => 'Tabungan',
                    'description' => 'Setoran pelunasan target',
                    'transaction_date' => Carbon::today()->subDays(12),
                ]);
            }
        }

        // 3. Seed Today's IoT Deposits (Rp1k, Rp2k, Rp5k, Rp10k)
        // Budi (Kelas 4) saves 10k today
        $goalBudi = SavingsGoal::where('user_id', $user4->id)->first();
        $goalBudi->current_amount += 10000;
        $goalBudi->save();

        Transaction::create([
            'savings_goal_id' => $goalBudi->id,
            'user_id' => $user4->id,
            'type' => 'deposit',
            'amount' => 10000,
            'category' => 'Tabungan IoT',
            'description' => 'Setoran otomatis via mesin Smart Savings',
            'transaction_date' => Carbon::today(),
        ]);

        // Ani (Kelas 5) saves 5k today
        $goalAni = SavingsGoal::where('user_id', $user5->id)->first();
        $goalAni->current_amount += 5000;
        $goalAni->save();

        Transaction::create([
            'savings_goal_id' => $goalAni->id,
            'user_id' => $user5->id,
            'type' => 'deposit',
            'amount' => 5000,
            'category' => 'Tabungan IoT',
            'description' => 'Setoran otomatis via mesin Smart Savings',
            'transaction_date' => Carbon::today(),
        ]);

        // Cici (Kelas 6) saves 2k today
        // Since her old goal is achieved, we can make a new active goal or just seed it as general transaction
        $goalCici = SavingsGoal::create([
            'user_id' => $user6->id,
            'title' => 'Tabungan Baru Cici',
            'target_amount' => 500000,
            'current_amount' => 2000,
            'target_date' => Carbon::today()->addMonths(4),
            'category' => 'Umum',
            'color' => '#8b5cf6',
            'icon' => 'fa-wallet',
            'description' => 'Tabungan setelah target pertama tercapai.',
            'status' => 'active',
        ]);

        Transaction::create([
            'savings_goal_id' => $goalCici->id,
            'user_id' => $user6->id,
            'type' => 'deposit',
            'amount' => 2000,
            'category' => 'Tabungan IoT',
            'description' => 'Setoran otomatis via mesin Smart Savings',
            'transaction_date' => Carbon::today(),
        ]);

        // 4. Seed 20 Transaksi untuk Raihan & 10 Transaksi untuk Rizal
        $this->call(RaihanRizalTransactionSeeder::class);
    }
}