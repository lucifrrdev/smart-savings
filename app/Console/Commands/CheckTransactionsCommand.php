<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Transaction;

class CheckTransactionsCommand extends Command
{
    protected $signature = 'check:transactions';
    protected $description = 'Check transaction count for Raihan and Rizal';

    public function handle()
    {
        $raihan = User::where('name', 'like', '%Raihan%')->first();
        $rizal = User::where('name', 'like', '%Rizal%')->first();

        if ($raihan) {
            $raihanCount = Transaction::where('user_id', $raihan->id)->count();
            $this->info("Raihan (ID: {$raihan->id}, Class: {$raihan->class}) Total Transaksi: {$raihanCount}");
        } else {
            $this->error("Raihan tidak ditemukan.");
        }

        if ($rizal) {
            $rizalCount = Transaction::where('user_id', $rizal->id)->count();
            $this->info("Rizal (ID: {$rizal->id}, Class: {$rizal->class}) Total Transaksi: {$rizalCount}");
        } else {
            $this->error("Rizal tidak ditemukan.");
        }
    }
}
