<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class IoTController extends Controller
{
    public function deposit(Request $request)
    {
        $request->validate([
            'nisn' => ['required', 'string'],
            'amount' => ['required', 'integer', 'in:1000,2000,5000,10000'],
        ]);

        $user = User::where('nisn', $request->nisn)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Siswa dengan NISN tersebut tidak ditemukan.'
            ], 404);
        }

        // Cari target tabungan aktif milik user
        $goal = SavingsGoal::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        // Jika tidak ada target aktif, otomatis buatkan "Tabungan Utama"
        if (!$goal) {
            $goal = SavingsGoal::create([
                'user_id' => $user->id,
                'title' => 'Tabungan Utama',
                'target_amount' => 10000000,
                'current_amount' => 0,
                'target_date' => Carbon::today()->addYear(),
                'category' => 'Umum',
                'color' => '#f53003',
                'icon' => 'fa-wallet',
                'description' => 'Target tabungan utama siswa.',
                'status' => 'active'
            ]);
        }

        // Update nominal terkumpul di target tabungan
        $goal->current_amount += $request->amount;
        
        // Tandai selesai jika target tercapai
        if ($goal->current_amount >= $goal->target_amount) {
            $goal->status = 'achieved';
        }
        $goal->save();

        // Catat transaksi baru
        $transaction = Transaction::create([
            'savings_goal_id' => $goal->id,
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => $request->amount,
            'category' => 'Tabungan IoT',
            'description' => 'Setoran otomatis via mesin Smart Savings',
            'transaction_date' => Carbon::today()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Uang berhasil ditabung!',
            'data' => [
                'nama' => $user->name,
                'kelas' => $user->class,
                'nominal_ditabung' => (int) $request->amount,
                'total_tabungan_target' => (int) $goal->current_amount,
                'target_nama' => $goal->title,
                'status_target' => $goal->status
            ]
        ], 200);
    }
}
