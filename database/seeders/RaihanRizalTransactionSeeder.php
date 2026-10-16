<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use App\Models\TransactionHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class RaihanRizalTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Inisialisasi atau temukan Siswa Raihan
        $raihan = User::where('name', 'like', '%Raihan%')
            ->orWhere('username', 'raihan')
            ->first();

        if (!$raihan) {
            $raihan = User::create([
                'name' => 'Raihan',
                'username' => 'raihan',
                'class' => 'Kelas 5',
                'nisn' => '0051234567',
                'birth_date' => '2015-06-15',
                'role' => 'siswa',
                'password' => Hash::make('password123'),
            ]);
        }

        // Target Tabungan untuk Raihan
        $goalRaihan = SavingsGoal::where('user_id', $raihan->id)
            ->where('status', 'active')
            ->first();

        if (!$goalRaihan) {
            $goalRaihan = SavingsGoal::create([
                'user_id' => $raihan->id,
                'title' => 'Tabungan Pendidikan Raihan',
                'target_amount' => 500000,
                'current_amount' => 0,
                'target_date' => Carbon::today()->addMonths(6),
                'category' => 'Pendidikan',
                'color' => '#f53003',
                'icon' => 'fa-graduation-cap',
                'description' => 'Target tabungan pendidikan dan perlengkapan sekolah Raihan.',
                'status' => 'active',
            ]);
        }

        // Hapus transaksi lama Raihan jika ingin memastikan tepat 20 transaksi baru,
        // atau kita hitung sisa yang dibutuhkan agar totalnya pas 20
        $currentRaihanTxCount = Transaction::where('user_id', $raihan->id)->count();
        $neededForRaihan = max(0, 20 - $currentRaihanTxCount);

        // Jika belum ada atau kurang dari 20, tambahkan hingga genap 20
        $raihanNominals = [
            5000, 10000, 2000, 5000, 10000, 
            15000, 5000, 20000, 10000, 5000,
            2000, 10000, 5000, 15000, 10000, 
            25000, 5000, 10000, 20000, 10000
        ];

        $raihanDescriptions = [
            'Setoran sisa uang jajan harian',
            'Tabungan uang saku sekolah',
            'Setoran koin Smart Savings IoT',
            'Tabungan reward belajar',
            'Setoran mingguan',
            'Sisa uang transport sekolah',
            'Setoran koin harian',
            'Tabungan dari hadiah kakek',
            'Setoran mandiri via mesin IoT',
            'Uang saku hari Jumat',
            'Setoran receh celengan',
            'Tabungan sisa jajan istirahat',
            'Setoran via mesin pintar Smart Savings',
            'Tabungan awal pekan',
            'Tabungan prestasi kelas',
            'Uang hadiah ulang tahun',
            'Setoran harian sekolah',
            'Tabungan rutin pagi',
            'Setoran celengan mingguan',
            'Tabungan sisa uang jajan akhir pekan'
        ];

        $totalRaihanAdded = 0;
        for ($i = 0; $i < 20; $i++) {
            $daysAgo = 20 - $i;
            $txDate = Carbon::today()->subDays($daysAgo);
            // Jadikan transaksi terakhir terjadi hari ini
            if ($i === 19) {
                $txDate = Carbon::today();
            }

            $amount = $raihanNominals[$i];
            $desc = $raihanDescriptions[$i];
            $category = ($i % 3 === 0) ? 'Tabungan IoT' : 'Tabungan';

            $tx = Transaction::create([
                'savings_goal_id' => $goalRaihan->id,
                'user_id' => $raihan->id,
                'type' => 'deposit',
                'amount' => $amount,
                'category' => $category,
                'description' => $desc,
                'transaction_date' => $txDate,
            ]);

            TransactionHistory::create([
                'savings_goal_id' => $goalRaihan->id,
                'user_id' => $raihan->id,
                'type' => 'deposit',
                'amount' => $amount,
                'category' => $category,
                'description' => $desc,
                'transaction_date' => $txDate,
            ]);

            $totalRaihanAdded += $amount;
        }

        $goalRaihan->current_amount += $totalRaihanAdded;
        $goalRaihan->save();


        // 2. Inisialisasi atau temukan Siswa Rizal
        $rizal = User::where('name', 'like', '%Rizal%')
            ->orWhere('username', 'rizal')
            ->first();

        if (!$rizal) {
            $rizal = User::create([
                'name' => 'Rizal',
                'username' => 'rizal',
                'class' => 'Kelas 4',
                'nisn' => '0049876543',
                'birth_date' => '2016-04-18',
                'role' => 'siswa',
                'password' => Hash::make('password123'),
            ]);
        }

        // Target Tabungan untuk Rizal
        $goalRizal = SavingsGoal::where('user_id', $rizal->id)
            ->where('status', 'active')
            ->first();

        if (!$goalRizal) {
            $goalRizal = SavingsGoal::create([
                'user_id' => $rizal->id,
                'title' => 'Buku Ensiklopedia Rizal',
                'target_amount' => 250000,
                'current_amount' => 0,
                'target_date' => Carbon::today()->addMonths(4),
                'category' => 'Buku',
                'color' => '#10b981',
                'icon' => 'fa-book',
                'description' => 'Target tabungan untuk membeli buku ensiklopedia sains anak.',
                'status' => 'active',
            ]);
        }

        $rizalNominals = [
            5000, 10000, 2000, 15000, 5000,
            10000, 5000, 20000, 10000, 15000
        ];

        $rizalDescriptions = [
            'Setoran awal tabungan buku',
            'Sisa uang saku sekolah',
            'Koin kembalian belanja',
            'Tabungan sisa uang jajan',
            'Setoran via mesin Smart Savings',
            'Uang saku hari Selasa',
            'Koin celengan harian',
            'Tabungan dari hadiah lomba',
            'Tabungan rutin akhir pekan',
            'Setoran uang saku hari ini'
        ];

        $totalRizalAdded = 0;
        for ($j = 0; $j < 10; $j++) {
            $daysAgo = 10 - $j;
            $txDate = Carbon::today()->subDays($daysAgo);
            if ($j === 9) {
                $txDate = Carbon::today();
            }

            $amount = $rizalNominals[$j];
            $desc = $rizalDescriptions[$j];
            $category = ($j % 2 === 0) ? 'Tabungan IoT' : 'Tabungan';

            $tx = Transaction::create([
                'savings_goal_id' => $goalRizal->id,
                'user_id' => $rizal->id,
                'type' => 'deposit',
                'amount' => $amount,
                'category' => $category,
                'description' => $desc,
                'transaction_date' => $txDate,
            ]);

            TransactionHistory::create([
                'savings_goal_id' => $goalRizal->id,
                'user_id' => $rizal->id,
                'type' => 'deposit',
                'amount' => $amount,
                'category' => $category,
                'description' => $desc,
                'transaction_date' => $txDate,
            ]);

            $totalRizalAdded += $amount;
        }

        $goalRizal->current_amount += $totalRizalAdded;
        $goalRizal->save();
    }
}
